@extends('layouts.app')

@section('title', 'Register | Grand Stay')

@section('content')
<main class="auth-wrap">
    <div class="auth-card">
        <h1>Create an account</h1>
        <p class="muted">Register to book rooms and services.</p>

        <form method="POST" action="{{ route('register') }}" data-once>
            @csrf

            <label for="name">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            @error('name') <small class="field-error">{{ $message }}</small> @enderror

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email') <small class="field-error">{{ $message }}</small> @enderror

            <div class="">
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password">
                    @error('password') <small class="field-error">{{ $message }}</small> @enderror

                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <label for="phone">Phone (optional)</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}">
                    @error('phone') <small class="field-error">{{ $message }}</small> @enderror
                </div>
                <div>
                    <label for="address">Address (optional)</label>
                    <input id="address" type="text" name="address" value="{{ old('address') }}">
                    @error('address') <small class="field-error">{{ $message }}</small> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-full" data-loading="Creating account...">Create account</button>
        </form>

        <p class="switch">Already registered? <a href="{{ route('login') }}">Log in</a></p>
    </div>
</main>
@endsection