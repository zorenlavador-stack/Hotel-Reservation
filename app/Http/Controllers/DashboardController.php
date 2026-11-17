<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load([
            'guestProfile',
            'reservations' => fn ($query) => $query->latest(),
            'reservations.room',
            'reservations.payments',
            'reservations.services',
        ]);

        $rooms = Room::where('status', 'available')->orderBy('room_number')->get();

        // Room clicked on the landing page (?room=2). Null if missing or not available.
        $selectedRoom = $rooms->firstWhere('id', (int) $request->query('room'));

        // One-time reservation token (kept from your version)
        $token = (string) Str::uuid();
        session(['reservation_token' => $token]);

        return view('dashboard', [
            'user'         => $user,
            'rooms'        => $rooms,
            'selectedRoom' => $selectedRoom,
            'services'     => Service::where('status', 'available')->orderBy('name')->get(),
            'token'        => $token,
        ]);
    }
}