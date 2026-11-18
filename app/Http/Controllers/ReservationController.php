<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'form_token' => ['required', 'string'],
            'room_id'    => ['required', 'exists:rooms,id'],
            'check_in'   => ['required', 'date', 'after_or_equal:today'],
            'check_out'  => ['required', 'date', 'after:check_in'],
            'services'   => ['nullable', 'array'],
            'services.*' => ['exists:services,id'],
        ]);

        // Layer 2: the token can be used once. A second click or request is ignored silently.
        $saved = (string) $request->session()->pull('reservation_token');

        if ($saved === '' || ! hash_equals($saved, $data['form_token'])) {
            return redirect()->route('dashboard');
        }

        $reservation = DB::transaction(function () use ($request, $data) {
            // Layer 3: lock the room row so two requests can't check availability at the same time
            $room = Room::lockForUpdate()->findOrFail($data['room_id']);

            $taken = $room->reservations()
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('check_in', '<', $data['check_out'])
                ->where('check_out', '>', $data['check_in'])
                ->exists();

            if ($taken) {
                throw ValidationException::withMessages([
                    'room_id' => 'That room is already booked for those dates.',
                ]);
            }

            $nights   = (int) Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out']));
            $services = Service::whereIn('id', $data['services'] ?? [])->get();
            $total    = ($nights * $room->price_per_night) + $services->sum('price');

            // One to Many: user_id is filled in automatically
            $reservation = $request->user()->reservations()->create([
                'room_id'      => $room->id,
                'check_in'     => $data['check_in'],
                'check_out'    => $data['check_out'],
                'status'       => 'pending',
                'total_amount' => $total,
            ]);

            // Many to Many: one row per service in reservation_services
            $reservation->services()->attach(
                $services->mapWithKeys(fn($s) => [$s->id => ['quantity' => 1, 'price' => $s->price]])->all()
            );

            return $reservation;
        });

        // Post/Redirect/Get
        return redirect()->route('dashboard')
            ->with('status', 'Reservation created. Total: ₱' . number_format($reservation->total_amount, 2));
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        // A user can only cancel their own reservation
        abort_unless($reservation->user_id === $request->user()->id, 403);

        $refunded = DB::transaction(function () use ($reservation) {
            // Lock the row so two clicks cannot cancel (and refund) twice
            $locked = Reservation::lockForUpdate()->findOrFail($reservation->id);

            // Already cancelled (double click or repeated request): do nothing
            if ($locked->status === 'cancelled') {
                return null;
            }

            if (! $locked->canBeCancelled()) {
                throw ValidationException::withMessages([
                    'cancel' => 'This reservation can no longer be cancelled.',
                ])->errorBag('cancel');
            }

            $paid = (float) $locked->payments()->where('status', 'paid')->sum('amount');

            // Demo: payments are only marked as refunded, no money moves
            $locked->payments()->where('status', 'paid')->update(['status' => 'refunded']);
            $locked->update(['status' => 'cancelled']);

            return $paid;
        });

        if ($refunded === null) {
            return redirect()->route('dashboard');
        }

        $message = 'Reservation cancelled.'
            . ($refunded > 0 ? ' Payments of ₱' . number_format($refunded, 2) . ' were marked as refunded (demo).' : '');

        // Post/Redirect/Get: refreshing never repeats the cancellation
        return redirect()->route('dashboard')->with('status', $message);
    }
}
