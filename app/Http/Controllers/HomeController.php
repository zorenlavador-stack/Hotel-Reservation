<?php

namespace App\Http\Controllers;

use App\Models\Room;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'rooms' => Room::where('status', 'available')
                ->orderBy('price_per_night')
                ->get(),
        ]);
    }
}