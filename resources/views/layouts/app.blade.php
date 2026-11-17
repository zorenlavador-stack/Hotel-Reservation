<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Grand Stay')</title>
    <link rel="stylesheet" href="{{ asset('css/hotel.css') }}">
</head>
<body>

    <nav class="nav">
        <a href="{{ route('home') }}" class="logo">GRAND<span>STAY.</span></a>

        <div class="nav-links">
            @auth
                <span class="nav-user">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a href="{{ route('register') }}" class="btn">Register</a>
            @endauth
        </div>
    </nav>

    @yield('content')

    <script>
        // Layer 1: disable the button after the first submit
        document.querySelectorAll('form[data-once]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.dataset.submitted) {
                    e.preventDefault();
                    return;
                }
                form.dataset.submitted = 'true';

                var btn = form.querySelector('button[type=submit]');
                btn.disabled = true;
                btn.textContent = btn.dataset.loading || 'Please wait...';
            });
        });

        // If the browser restores an old page from the Back button, reload it
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) location.reload();
        });
    </script>
</body>
</html>