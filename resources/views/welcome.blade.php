<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grand Stay | Hotel Reservation</title>
    <link rel="stylesheet" href="{{ asset('css/hotel.css') }}">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="nav">
        <a href="{{ route('home') }}" class="logo">GRAND<span>STAY.</span></a>

        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#rooms">Rooms</a>
            <a href="#services">Services</a>

            @auth
                <a href="{{ route('dashboard') }}" class="btn">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn">Login</a>
            @endauth
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <small>WELCOME TO GRAND STAY</small>

            <h1>Experience Comfort, Luxury & Relaxation.</h1>

            <p>
                Discover a perfect place to relax and enjoy.
                Book your stay with us and experience exceptional
                hospitality and comfortable accommodations.
            </p>

            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="primary-btn">
                Book Your Stay →
            </a>
        </div>
    </section>

    <!-- ROOMS SECTION -->
    <section class="section" id="rooms">
        <div class="section-title">
            <small>OUR ACCOMMODATIONS</small>
            <h2>Explore Our Rooms</h2>
        </div>

        <div class="rooms">
            @forelse ($rooms as $room)
            <a href="{{ route('dashboard', ['room' => $room->id]) }}" class="room-link">
                <div class="room-card">
                    <img src="{{ asset('images/' . $room->image) }}"
                        alt="{{ ucfirst($room->room_type) }} room {{ $room->room_number }}">

                    <div class="room-info">
                        <h3>{{ ucfirst($room->room_type) }} Room</h3>
                        <p>Room {{ $room->room_number }}. {{ $room->description }}</p>
                        <span class="price">₱{{ number_format($room->price_per_night) }} / night</span>
                        <span class="room-action">Reserve this room →</span>
                    </div>
                </div>
            </a>
            @empty
                <p class="muted">No rooms are available right now.</p>
            @endforelse
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section class="section" id="services">
        <div class="section-title">
            <small>WHAT WE OFFER</small>
            <h2>Our Hotel Services</h2>
        </div>

        <div class="services">
            <div class="service-card">
                <h3>Comfortable Rooms</h3>
                <p>Clean, relaxing, and well-equipped accommodations.</p>
            </div>

            <div class="service-card">
                <h3>Room Service</h3>
                <p>Convenient services to make your stay enjoyable.</p>
            </div>

            <div class="service-card">
                <h3>Easy Reservation</h3>
                <p>Simple and convenient online room booking.</p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>© 2026 <span>Grand Stay Hotel</span>. All Rights Reserved.</p>
    </footer>

</body>

</html>