<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'], // blocks duplicate accounts
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Both inserts succeed together or neither is saved
        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // One to One: creates the guest_profiles row with user_id filled in
            $user->guestProfile()->create([
                'phone'   => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ]);
        });

        // Post/Redirect/Get: refreshing now repeats a GET, not the POST
        return redirect()->route('login')
            ->with('status', 'Account created. You can log in now.');
    }
}