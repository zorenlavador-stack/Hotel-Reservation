@extends('layouts.app')

@section('title', 'Log in | Grand Stay')

@section('content')
<main class="auth-wrap">
    <div class="auth-card">
        <h1>Log in</h1>
        <p class="muted">Welcome back. Log in to manage your reservations.</p>

        @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" data-once>
            @csrf

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">

            <label class="check">
                <input type="checkbox" name="remember"> Remember me
            </label>

            <button type="submit" id="submitBtn" class="btn btn-full" data-loading="Logging in..." @if (session('lockout')) data-lockout-seconds="{{ (int) session('lockout') }}" @endif>Log in</button>
        </form>

        <p class="switch">No account yet? <a href="{{ route('register') }}">Register</a></p>
    </div>
</main>

@if (session('lockout'))
<script>
    // Countdown while locked out. Only for show: the server enforces the lock.
    const btn = document.getElementById('submitBtn');
    let seconds = Number(btn.dataset.lockoutSeconds || 0);

    btn.disabled = true;
    btn.textContent = 'Try again in ' + seconds + 's';

    const timer = setInterval(function() {
        seconds--;
        if (seconds <= 0) {
            clearInterval(timer);
            btn.disabled = false;
            btn.textContent = 'Log in';
        } else {
            btn.textContent = 'Try again in ' + seconds + 's';
        }
    }, 1000);
</script>
@endif
@endsection