<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    private const METHODS = [
        'cash'  => 'Cash',
        'card'  => 'Card',
        'gcash' => 'GCash',
    ];

    public function store(Request $request, Reservation $reservation)
    {
        // A user can only pay for their own reservation
        abort_unless($reservation->user_id === $request->user()->id, 403);

        $data = $request->validateWithBag('payment', [
            'payment_token'  => ['required', 'string'],
            'payment_method' => ['required', Rule::in(array_keys(self::METHODS))],
            'amount'         => ['required', 'numeric', 'min:1'],
        ]);

        // Layer 2: one-time token. A second click or repeated request is ignored silently.
        $saved = (string) $request->session()->pull('payment_token');

        if ($saved === '' || ! hash_equals($saved, $data['payment_token'])) {
            return redirect()->route('dashboard');
        }

        [$payment, $remaining] = DB::transaction(function () use ($reservation, $data) {
            // Layer 3: lock the reservation row, then re-calculate the balance from the database
            $locked = Reservation::lockForUpdate()->findOrFail($reservation->id);

            $paid    = (float) $locked->payments()->where('status', 'paid')->sum('amount');
            $balance = round((float) $locked->total_amount - $paid, 2);
            $amount  = round((float) $data['amount'], 2);

            if ($balance <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'This reservation is already paid in full.',
                ])->errorBag('payment');
            }

            if ($amount > $balance) {
                throw ValidationException::withMessages([
                    'amount' => 'The amount is more than the balance of ₱' . number_format($balance, 2) . '.',
                ])->errorBag('payment');
            }

            if ($locked->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'amount' => 'This reservation was cancelled, so it can no longer be paid.',
                ])->errorBag('payment');
            }

            // One to Many: reservation_id is filled in automatically
            $payment = $locked->payments()->create([
                'amount'         => $amount,
                'payment_method' => $data['payment_method'],
                'status'         => 'paid',                              // demo: always succeeds
                'transaction_id' => 'TMP-' . strtoupper(Str::random(10)), // demo reference number
            ]);

            $remaining = round($balance - $amount, 2);

            if ($remaining <= 0) {
                $locked->update(['status' => 'confirmed']);
            }

            return [$payment, $remaining];
        });

        $message = 'Payment of ₱' . number_format($payment->amount, 2)
            . ' received via ' . self::METHODS[$payment->payment_method] . '. '
            . ($remaining > 0
                ? 'Remaining balance: ₱' . number_format($remaining, 2) . '.'
                : 'Your reservation is paid in full and confirmed.');

        // Post/Redirect/Get: refreshing never resubmits the payment
        return redirect()->route('dashboard')->with('status', $message);
    }
}
