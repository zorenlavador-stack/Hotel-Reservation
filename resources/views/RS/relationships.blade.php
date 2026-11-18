<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eloquent Relationships | Grand Stay</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1b263b;
        }

        nav {
            background: #14213d;
            padding: 20px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .logo span,
        footer span {
            color: #d4a017;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .heading {
            margin-bottom: 35px;
        }

        .heading small {
            color: #c29218;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .heading h1 {
            font-size: 32px;
            margin: 12px 0;
        }

        .heading p {
            color: #777;
            line-height: 1.6;
        }

        .relationship-card {
            background: white;
            padding: 28px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #e8eaf0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .03);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .card-header h2 {
            font-size: 21px;
        }

        .number {
            color: #d4a017;
            font-weight: bold;
            font-size: 25px;
        }

        .tag {
            background: #edf2fa;
            color: #14213d;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .description {
            color: #777;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        /* DATABASE RECORDS */

        .data-title {
            font-size: 15px;
            font-weight: bold;
            margin: 20px 0 12px;
        }

        .record {
            background: #f7f8fb;
            border: 1px solid #e8eaf0;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .record-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            gap: 10px;
        }

        .record-header h3 {
            font-size: 15px;
        }

        .id-badge {
            background: #14213d;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 11px;
        }

        .record p {
            color: #555;
            font-size: 13px;
            margin: 7px 0;
        }

        .child-record {
            background: white;
            border-left: 3px solid #d4a017;
            padding: 12px;
            margin: 10px 0 0 15px;
            border-radius: 5px;
        }

        .child-record h4 {
            font-size: 13px;
            margin-bottom: 7px;
        }

        .empty {
            padding: 15px;
            background: #fff8e7;
            color: #80651f;
            border-radius: 6px;
            font-size: 13px;
        }

        .diagram {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            padding: 20px;
            background: #f7f8fb;
            border-radius: 8px;
        }

        .table-box {
            background: #14213d;
            color: white;
            padding: 15px 25px;
            border-radius: 7px;
            text-align: center;
            min-width: 130px;
        }

        .table-box small {
            display: block;
            color: #d4a017;
            margin-top: 6px;
        }

        .arrow {
            color: #d4a017;
            font-size: 25px;
        }

        .code-title {
            margin: 22px 0 10px;
            font-size: 14px;
            font-weight: bold;
        }

        /* SHOW / HIDE CODE BUTTON */


        .code-toggle {
            margin-top: 22px;
        }

        .code-toggle summary {
            display: inline-block;
            background: #14213d;
            color: white;
            padding: 12px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            list-style: none;
            transition: 0.3s;
        }

        .code-toggle summary:hover {
            background: #253b63;
        }

        .code-toggle summary::-webkit-details-marker {
            display: none;
        }

        .code-toggle summary::before {
            content: "+ ";
            color: #d4a017;
            font-size: 17px;
        }

        .code-toggle[open] summary::before {
            content: "− ";
        }

        .code-toggle pre {
            margin-top: 12px;
        }

        pre {
            background: #17243b;
            color: #e7edf7;
            padding: 18px;
            border-radius: 7px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.7;
        }

        .note {
            background: #fff8e7;
            border-left: 4px solid #d4a017;
            padding: 13px;
            margin-top: 18px;
            font-size: 13px;
            color: #66521c;
        }

        footer {
            text-align: center;
            background: #14213d;
            color: white;
            padding: 22px;
            margin-top: 50px;
            font-size: 13px;
        }

        @media(max-width: 600px) {
            .heading h1 {
                font-size: 25px;
            }

            .card-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .relationship-card {
                padding: 20px;
            }

            .diagram {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav>
        <div class="logo">GRAND<span>STAY.</span></div>
        <a href="{{ url('/') }}">← Back to Home</a>
    </nav>

    <main class="container">

        <!-- PAGE HEADING -->
        <div class="heading">
            <small>DATABASE STRUCTURE</small>
            <h1>Eloquent Relationships</h1>
            <p>
                Learn how Laravel Eloquent connects different database
                tables using One-to-One, One-to-Many, and Many-to-Many
                relationships.
            </p>
        </div>

        <!-- ================================= -->
        <!-- 01. ONE TO ONE -->
        <!-- ================================= -->

        <section class="relationship-card">

            <p class="description">
                One user has one guest profile. This relationship
                connects one record to another single record.
            </p>

            <!-- DATABASE RECORDS -->

            <h3 class="data-title">Database Records: Users & Guest Profiles</h3>

            @forelse ($users as $user)

            <div class="record">

                <!-- <div class="record-header">
                    <h3>{{ $user->name }}</h3>
                    <span class="id-badge">User #{{ $user->id }}</span>
                </div> -->

                <p><strong>Email:</strong> {{ $user->email }}</p>

                @if ($user->guestProfile)

                <div class="child-record">
                    <h4>Guest Profile</h4>

                    <p>
                        <strong>Profile ID:</strong>
                        {{ $user->guestProfile->id }}
                    </p>

                    <p>
                        <strong>User ID:</strong>
                        {{ $user->guestProfile->user_id }}
                    </p>
                </div>

                @else

                <div class="empty">
                    No guest profile found for this user.
                </div>

                @endif

            </div>

            @empty

            <div class="empty">No users found in the database.</div>

            @endforelse


            <details class="code-toggle">

                <summary>Show Laravel Model Relationship</summary>

                <pre>// User.php

public function guestProfile()
{
    return $this->hasOne(GuestProfile::class);
}

// GuestProfile.php

public function user()
{
    return $this->belongsTo(User::class);

}

</pre>
            </details>

            <!-- <div class="note">
                <strong>Key Point:</strong>
                The guest_profiles table contains user_id
                as its foreign key.
            </div> -->

        </section>


        <!-- ================================= -->
        <!-- 02. ONE TO MANY -->
        <!-- ================================= -->

        <section class="relationship-card">

            <p class="description">
                One user can create multiple hotel reservations.
                Each reservation belongs to only one user.
            </p>

            <!-- DATABASE RECORDS -->

            <h3 class="data-title">Database Records: Users & Reservations</h3>

            @forelse ($users as $user)

            <!-- <div class="record">

                <div class="record-header">
                    <h3>{{ $user->name }}</h3>
                    <span class="id-badge">User #{{ $user->id }}</span>
                </div> -->

                <p><strong>Email:</strong> {{ $user->email }}</p>

                @forelse ($user->reservations as $reservation)

                <div class="child-record">

                    <h4>
                        Reservation #{{ $reservation->id }}
                    </h4>

                    <p>
                        <strong>Room ID:</strong>
                        {{ $reservation->room_id }}
                    </p>

                    <p>
                        <strong>Check-in:</strong>
                        {{ $reservation->check_in }}
                    </p>

                    <p>
                        <strong>Check-out:</strong>
                        {{ $reservation->check_out }}
                    </p>

                    <p>
                        <strong>Status:</strong>
                        {{ ucfirst($reservation->status) }}
                    </p>

                    <p>
                        <strong>Total Amount:</strong>
                        ₱{{ number_format($reservation->total_amount, 2) }}
                    </p>

                </div>

                @empty

                <div class="empty">
                    This user has no reservations yet.
                </div>

                @endforelse

            </div>

            @empty

            <div class="empty">No users found in the database.</div>

            @endforelse


            <details class="code-toggle">

                <summary>Show Laravel Model Relationship</summary>

                <pre>// User.php

public function reservations()
{
    return $this->hasMany(Reservation::class);
}

// Reservation.php

public function user()
{
    return $this->belongsTo(User::class);

}

</pre>
            </details>

            <!-- <div class="note">
                <strong>Key Point:</strong>
                The reservations table contains user_id
                as its foreign key.
            </div> -->

        </section>


        <!-- ================================= -->
        <!-- 03. MANY TO MANY -->
        <!-- ================================= -->

        <section class="relationship-card">

            <p class="description">
                Multiple reservations can have multiple services.
                Laravel uses a pivot table called reservation_services
                to connect these two tables.
            </p>

            <!-- DATABASE RECORDS -->

            <h3 class="data-title">
                Database Records: Reservations & Services
            </h3>

            @forelse ($reservations as $reservation)

            <div class="record">

                <!-- <div class="record-header">
                    <h3>Reservation #{{ $reservation->id }}</h3>
                    <span class="id-badge">
                        User #{{ $reservation->user_id }}
                    </span>
                </div> -->

                <p>
                    <strong>Guest:</strong>
                    {{ $reservation->user?->name ?? 'User not found' }}
                </p>

                <p>
                    <strong>Check-in:</strong>
                    {{ $reservation->check_in }}
                </p>

                @forelse ($reservation->services as $service)

                <div class="child-record">

                    <h4>{{ $service->name }}</h4>

                    <p>
                        <strong>Service ID:</strong>
                        {{ $service->id }}
                    </p>

                    <p>
                        <strong>Quantity:</strong>
                        {{ $service->pivot->quantity }}
                    </p>

                    <p>
                        <strong>Price:</strong>
                        ₱{{ number_format($service->pivot->price, 2) }}
                    </p>

                    <p>
                        <strong>Subtotal:</strong>
                        ₱{{ number_format(
                                    $service->pivot->quantity *
                                    $service->pivot->price, 2
                                ) }}
                    </p>

                </div>

                @empty

                <div class="empty">
                    No services added to this reservation.
                </div>

                @endforelse

            </div>

            @empty

            <div class="empty">
                No reservations found in the database.
            </div>

            @endforelse


            <details class="code-toggle">

                <summary>Show Laravel Model Relationship</summary>

                <pre>// Reservation.php

public function services()
{
    return $this->belongsToMany(
        Service::class,
        'reservation_services',
        'reservation_id',
        'service_id'
    )
    ->withPivot('quantity', 'price')
    ->withTimestamps();
}

// Service.php

public function reservations()
{
    return $this->belongsToMany(
        Reservation::class,
        'reservation_services',
        'service_id',
        'reservation_id'
    )
    ->withPivot('quantity', 'price')
    ->withTimestamps();
}</pre>

            </details>

            <!-- <div class="note">
                <strong>Key Point:</strong>
                The reservation_services pivot table connects
                reservations and services. It also stores
                quantity and price.
            </div> -->

        </section>

    </main>

    <!-- FOOTER -->
    <footer>
        © 2026 <span>Grand Stay Hotel</span>. All Rights Reserved.
    </footer>

</body>

</html>