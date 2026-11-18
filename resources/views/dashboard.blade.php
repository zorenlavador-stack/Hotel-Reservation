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
                        required>

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
                        required>

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

        @php
        $alert = $errors->getBag('payment')->first() ?: $errors->getBag('cancel')->first();
        @endphp

        @if ($alert)
        <div class="alert alert-error">{{ $alert }}</div>
        @endif

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
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($user->reservations as $r)
                    <tr>
                        <td>{{ $r->room->room_number }} ({{ ucfirst($r->room->room_type) }})</td>

                        <td>
                            {{ $r->check_in->format('M d, Y') }}
                            to
                            {{ $r->check_out->format('M d, Y') }}
                        </td>

                        <td>{{ $r->services->pluck('name')->join(', ') ?: 'None' }}</td>

                        <td>₱{{ number_format($r->total_amount, 2) }}</td>
                        <td>₱{{ number_format($r->amount_paid, 2) }}</td>
                        <td>₱{{ number_format($r->balance, 2) }}</td>

                        <td><span class="badge badge-{{ $r->status }}">{{ ucfirst($r->status) }}</span></td>

                        <td>
                            <div class="row-actions">

                                @if ($r->status === 'cancelled')

                                <span class="muted-text">Cancelled</span>

                                @else

                                @if ($r->balance > 0)
                                <button type="button" class="btn btn-sm" data-pay
                                    data-action="{{ route('payments.store', $r) }}"
                                    data-balance="{{ number_format($r->balance, 2, '.', '') }}"
                                    data-balance-text="{{ number_format($r->balance, 2) }}"
                                    data-info="Room {{ $r->room->room_number }} ({{ ucfirst($r->room->room_type) }}), {{ $r->check_in->format('M d') }} to {{ $r->check_out->format('M d, Y') }}">
                                    Pay now
                                </button>
                                @else
                                <span class="badge badge-paid">Paid in full</span>
                                @endif

                                @if ($r->canBeCancelled())
                                <button type="button" class="btn btn-sm btn-danger-outline" data-cancel
                                    data-action="{{ route('reservations.cancel', $r) }}"
                                    data-info="Room {{ $r->room->room_number }} ({{ ucfirst($r->room->room_type) }}), {{ $r->check_in->format('M d') }} to {{ $r->check_out->format('M d, Y') }}"
                                    data-note="{{ $r->amount_paid > 0 ? 'Payments of ₱' . number_format($r->amount_paid, 2) . ' will be marked as refunded (demo).' : 'No payment has been made yet.' }}">
                                    Cancel
                                </button>
                                @endif

                                @endif

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        @endif

    </section>

    {{-- PAYMENT MODAL (one shared window, filled in by the script below) --}}
    <dialog id="payModal" class="modal">
        <form method="POST" id="payForm" action="" data-once>
            @csrf
            <input type="hidden" name="payment_token" value="{{ $paymentToken }}">

            <div class="modal-head">
                <h3>Pay for reservation</h3>
                <button type="button" class="modal-close" data-close aria-label="Close">&times;</button>
            </div>

            <p class="modal-sub" id="payInfo"></p>
            <p class="modal-sub" id="payBalance"></p>

            <label for="pay_method">Payment method</label>
            <select id="pay_method" name="payment_method" required>
                <option value="">Choose a method</option>
                <option value="cash">Cash (at the front desk)</option>
                <option value="card">Credit or debit card</option>
                <option value="gcash">GCash</option>
            </select>

            <label for="pay_amount">Amount (₱)</label>
            <input id="pay_amount" type="number" name="amount" min="1" step="0.01" required>

            <p class="pay-note">Demo payment. No real money is charged.</p>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" data-close>Not now</button>
                <button type="submit" class="btn" data-loading="Processing...">Confirm payment</button>
            </div>
        </form>
    </dialog>

    {{-- CANCEL MODAL --}}
    <dialog id="cancelModal" class="modal">
        <form method="POST" id="cancelForm" action="" data-once>
            @csrf

            <div class="modal-head">
                <h3>Cancel this reservation?</h3>
                <button type="button" class="modal-close" data-close aria-label="Close">&times;</button>
            </div>

            <p class="modal-sub" id="cancelInfo"></p>
            <p class="modal-sub">The room becomes available again for those dates.</p>
            <p class="modal-sub" id="cancelNote"></p>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" data-close>Keep reservation</button>
                <button type="submit" class="btn btn-danger" data-loading="Cancelling...">Yes, cancel it</button>
            </div>
        </form>
    </dialog>

    <script src="{{ asset('js/dashboard.js') }}" defer></script>

</main>
@endsection