<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 60;

    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // One counter per email + IP address
        $key = Str::lower($credentials['email']) . '|' . $request->ip();

        // Checked BEFORE the password, so even the correct password is refused while locked
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Too many login attempts. Try again in {$seconds} seconds."])
                ->with('lockout', $seconds);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);                 // reset the counter after a successful login
            $request->session()->regenerate();        // new session id (prevents session fixation)

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($key, self::LOCKOUT_SECONDS); // count this failed attempt
        $left = RateLimiter::remaining($key, self::MAX_ATTEMPTS);

        // Same message for a wrong email or a wrong password, so attackers learn nothing
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => "Email or password is incorrect. {$left} attempt(s) left."]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}