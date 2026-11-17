
@extends('layouts.app')

@section('title', 'Dashboard | Grand Stay')

@section('content')

<main class="container">

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <h1>Hello, {{ $user->name }}</h1>

    <p class="muted">
        Phone: {{ $user->guestProfile?->phone ?? 'not saved' }}.
        Address: {{ $user->guestProfile?->address ?? 'not saved' }}.
    </p>

    {{-- NEW RESERVATION --}}
    <section class="card">

        <h2>New reservation</h2>
        @if ($rooms->isEmpty())
            <p class="muted">No rooms have been added yet.</p>
        @else
            <form method="POST"
                  action="{{ route('reservations.store') }}"
                  data-once>
                @csrf
                <input type="hidden"
                       name="form_token"
                       value="{{ $token }}">

                {{-- ROOM SELECTION --}}
                <label for="room_id">Select Room</label>

                <select id="room_id" name="room_id" required>
                    <option value="">Choose a room</option>
                    @foreach ($rooms as $room)

                        <option
                            value="{{ $room->id }}"
                            @selected(old('room_id', $selectedRoom?->id) == $room->id)
                            @disabled(strtolower($room->status) !== 'available')
                        >
                            Room {{ $room->room_number }}
                            - {{ ucfirst($room->room_type) }}

                            (₱{{ number_format($room->price_per_night, 2) }} / night)

                            @if (strtolower($room->status) !== 'available')
                                - Unavailable
                            @endif
                        </option>
                    @endforeach
                </select>

                @error('room_id')
                    <small class="field-error">{{ $message }}</small>
                @enderror

                {{-- SELECTED ROOM INFORMATION --}}
                @if ($selectedRoom)
                    <div class="room-selection-info">
                        <h3>
                            Selected Room: {{ $selectedRoom->room_number }}
                        </h3>
                        <p>
                            {{ ucfirst($selectedRoom->room_type) }} Room
                        </p>
                        <p>
                            ₱{{ number_format($selectedRoom->price_per_night, 2) }} / night
                        </p>
                        @if (strtolower($selectedRoom->status) === 'available')

                            <span class="badge">Available for booking</span>
                        @else
                            <span class="badge">Currently unavailable</span>
                        @endif
                    </div>
                @endif

                {{-- CHECK-IN AND CHECK-OUT --}}
                <div class="grid-2">

                    <div>
                        <label for="check_in">Check-in</label>

                        <input
                            id="check_in"
                            type="date"
                            name="check_in"
                            value="{{ old('check_in') }}"
                            min="{{ today()->toDateString() }}"
                            required
                        >

                        @error('check_in')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label for="check_out">Check-out</label>

                        <input
                            id="check_out"
                            type="date"
                            name="check_out"
                            value="{{ old('check_out') }}"
                            min="{{ today()->toDateString() }}"
                            required
                        >

                        @error('check_out')
                            <small class="field-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                {{-- EXTRA SERVICES --}}
                @if ($services->isNotEmpty())

                    <label>Extra services (optional)</label>

                    <div class="checks">

                        @foreach ($services as $service)

                            <label class="check">

                                <input
                                    type="checkbox"
                                    name="services[]"
                                    value="{{ $service->id }}"
                                    @checked(in_array($service->id, old('services', [])))
                                >

                                {{ $service->name }}
                                (₱{{ number_format($service->price, 2) }})

                            </label>

                        @endforeach

                    </div>

                @endif

                <button type="submit"
                        class="btn"
                        data-loading="Saving...">
                    Reserve room
                </button>

            </form>

        @endif

    </section>

    {{-- MY RESERVATIONS --}}
    <section class="card">

        <h2>My reservations</h2>
        @if ($user->reservations->isEmpty())
            <p class="muted">
                You have no reservations yet.
                Use the form above to book a room.
            </p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Stay</th>
                            <th>Services</th>
                            <th>Paid</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($user->reservations as $r)
                            <tr>
                                <td>
                                    {{ $r->room->room_number }}
                                    ({{ ucfirst($r->room->room_type) }})
                                </td>
                                <td>
                                    {{ $r->check_in->format('M d, Y') }}
                                    to
                                    {{ $r->check_out->format('M d, Y') }}
                                </td>
                                <td>
                                    {{ $r->services->pluck('name')->join(', ') ?: 'None' }}
                                </td>
                                <td>
                                    ₱{{ number_format($r->payments->sum('amount'), 2) }}
                                </td>
                                <td>
                                    ₱{{ number_format($r->total_amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</main>

@endsection
