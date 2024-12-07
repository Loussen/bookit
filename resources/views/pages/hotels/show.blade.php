@extends('layouts.app')

@section('content')
    <div class="hotel-page">
        <!-- Header -->
        <div class="page-header">
            <div class="header-left" onclick="history.back()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </div>
            <div class="header-title">{{ $hotel->name }}</div>
            <div class="header-right">⋮</div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Hotel Image -->
            <div class="hotel-image">
                <img src="{{ $hotel->photo_url }}" alt="{{ $hotel->name }}">
                <div class="hotel-rating">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#FFD700" stroke="#FFD700">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    {{ $hotel->rating }}
                </div>
            </div>

            <!-- Hotel Info -->
            <div class="hotel-info">
                <div class="location">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    {{ $hotel->address }}
                </div>
                <p class="description">{{ $hotel->description }}</p>
            </div>

            <!-- Available Rooms -->
            <div class="rooms-section">
                <h2>Available Rooms</h2>

                @foreach($hotel->rooms as $room)
                    <div class="room-card">
                        <div class="room-image">
                            @if($room->photos->count() > 0)
                                <img src="{{ $room->photos->first()->photo_url }}" alt="{{ $room->name }}">
                            @endif
                        </div>

                        <div class="room-info">
                            <h3>{{ $room->name }}</h3>

                            <div class="room-features">
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                            {{ $room->capacity }} Guests
                        </span>
                                <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="2" y="4" width="20" height="16" rx="2"/>
                                <path d="M6 8h12M6 12h12M6 16h12"/>
                            </svg>
                            {{ $room->bed_type }}
                        </span>
                            </div>

                            <p class="room-description">{{ $room->description }}</p>

                            <div class="room-footer">
                                <div class="price">
                                    <span class="amount">${{ number_format($room->price, 2) }}</span>
                                    <span class="period">per night</span>
                                </div>

                                @if($room->is_available)
                                    <button class="book-button" onclick="showBookingDialog({{ $room->id }}, {{ $room->price }})">
                                        Book Now
                                    </button>
                                @else
                                    <button class="book-button disabled" disabled>Not Available</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Booking Dialog -->
        <div class="tg-dialog" id="bookingDialog">
            <div class="tg-dialog-header">
                <span class="close-btn" onclick="closeBookingDialog()">×</span>
                <span class="title">Book Room</span>
                <span></span>
            </div>

            <div class="tg-dialog-content">
                <form id="bookingForm">
                    <!-- Date Inputs -->
                    <div class="input-group">
                        <label for="checkIn">Check-in Date</label>
                        <input type="date" id="checkIn" name="checkIn" placeholder="Select a date">
                    </div>

                    <div class="input-group">
                        <label for="checkOut">Check-out Date</label>
                        <input type="date" id="checkOut" name="checkOut" placeholder="Select a date">
                    </div>

                    <!-- Guest Counter -->
                    <div class="input-group">
                        <label>Number of Guests</label>
                        <div class="guest-counter">
                            <button type="button" class="counter-btn minus" onclick="changeGuests(-1)">−</button>
                            <span class="guest-count" id="guestCountDisplay">1</span>
                            <button type="button" class="counter-btn plus" onclick="changeGuests(1)">+</button>
                        </div>
                        <input type="hidden" name="guests" id="guestCount" value="1">
                    </div>

                    <!-- Price Summary -->
                    <div class="price-summary">
                        <div class="summary-row">
                            <span>Room Rate</span>
                            <span class="value" id="roomRate">$776</span>
                        </div>
                        <div class="summary-row">
                            <span>Number of Nights</span>
                            <span class="value" id="nightCount">0</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total Price</span>
                            <span class="value" id="totalPrice">$0</span>
                        </div>
                    </div>

                    <button type="submit" class="confirm-button">Confirm Booking</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        :root {
            --tg-theme-bg-color: var(--tg-theme-bg-color, #fff);
            --tg-theme-text-color: var(--tg-theme-text-color, #000);
            --tg-theme-hint-color: var(--tg-theme-hint-color, #999);
            --tg-theme-link-color: var(--tg-theme-link-color, #2481cc);
            --tg-theme-button-color: var(--tg-theme-button-color, #2481cc);
            --tg-theme-button-text-color: var(--tg-theme-button-text-color, #fff);
        }

        .hotel-page {
            background: var(--tg-theme-bg-color);
            min-height: 100vh;
        }

        .page-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: var(--tg-theme-bg-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .header-title {
            font-size: 18px;
            font-weight: 600;
        }

        .main-content {
            padding-top: 56px;
        }

        .hotel-image {
            position: relative;
            height: 250px;
        }

        .hotel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hotel-rating {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .hotel-info {
            padding: 20px 16px;
        }

        .location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--tg-theme-hint-color);
            margin-bottom: 12px;
        }

        .rooms-section {
            padding: 0 16px;
        }

        .room-card {
            background: var(--tg-theme-bg-color);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .room-image {
            height: 200px;
        }

        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .room-info {
            padding: 16px;
        }

        .room-features {
            display: flex;
            gap: 16px;
            margin: 12px 0;
            color: var(--tg-theme-hint-color);
        }

        .room-features span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .room-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
        }

        .price .amount {
            font-size: 20px;
            font-weight: 600;
        }

        .price .period {
            color: var(--tg-theme-hint-color);
            font-size: 14px;
        }

        .book-button {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            background: var(--tg-theme-button-color);
            color: var(--tg-theme-button-text-color);
            font-weight: 500;
        }

        .book-button.disabled {
            background: var(--tg-theme-hint-color);
        }

        .tg-dialog {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--tg-theme-bg-color, #fff);
            z-index: 1000;
            display: none;
        }

        .tg-dialog-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            border-bottom: 1px solid var(--tg-theme-hint-color, rgba(0,0,0,0.1));
        }

        .close-btn {
            font-size: 24px;
            color: var(--tg-theme-hint-color, #8E8E93);
            cursor: pointer;
            width: 24px;
            text-align: center;
        }

        .title {
            font-size: 17px;
            font-weight: 600;
        }

        .tg-dialog-content {
            padding: 20px 16px;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-group label {
            display: block;
            color: var(--tg-theme-hint-color, #8E8E93);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .input-field {
            padding: 12px;
            background: var(--tg-theme-secondary-bg-color, #F2F2F7);
            border-radius: 10px;
            font-size: 17px;
            cursor: pointer;
        }

        .guest-counter {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 8px;
            background: var(--tg-theme-secondary-bg-color, #F2F2F7);
            border-radius: 10px;
        }

        .counter-btn {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 18px;
            background: var(--tg-theme-button-color, #007AFF);
            color: var(--tg-theme-button-text-color, #fff);
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .counter-btn:disabled {
            opacity: 0.5;
        }

        .guest-count {
            font-size: 17px;
            min-width: 30px;
            text-align: center;
        }

        .price-summary {
            background: var(--tg-theme-secondary-bg-color, #F2F2F7);
            border-radius: 10px;
            padding: 16px;
            margin: 24px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: var(--tg-theme-hint-color, #8E8E93);
        }

        .summary-row.total {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(0,0,0,0.1);
            color: var(--tg-theme-text-color, #000);
            font-weight: 600;
        }

        .confirm-button {
            width: 100%;
            padding: 16px;
            background: var(--tg-theme-button-color, #007AFF);
            color: var(--tg-theme-button-text-color, #fff);
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
        }

        input[type="date"] {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease-in-out;
            background-color: #fff;
            color: #333;
        }

        input[type="date"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        input[type="date"]::placeholder {
            color: #999;
        }
    </style>

    @push('scripts')
        <script>
            let currentRoom = null;
            const checkInInput = document.getElementById('checkIn');
            const checkOutInput = document.getElementById('checkOut');

            function showBookingDialog(roomId, price) {
                currentRoom = { id: roomId, price: price };

                // Reset form
                document.getElementById('bookingForm').reset();
                // document.getElementById('checkIn').html('Select date');
                // document.getElementById('checkOut').html('Select date');
                document.getElementById('roomRate').textContent = `$${price}`;
                document.getElementById('nightCount').textContent = '0';
                document.getElementById('totalPrice').textContent = '$0';

                // Show dialog
                const dialog = document.getElementById('bookingDialog');
                dialog.style.display = 'block';
                requestAnimationFrame(() => dialog.classList.add('active'));
            }

            function closeBookingDialog() {
                const dialog = document.getElementById('bookingDialog');
                dialog.classList.remove('active');
                setTimeout(() => dialog.style.display = 'none', 300);
            }

            function changeGuests(change) {
                const countDisplay = document.getElementById('guestCountDisplay');
                const countInput = document.getElementById('guestCount');
                const currentValue = parseInt(countInput.value);
                const newValue = Math.max(1, Math.min(10, currentValue + change));

                countDisplay.textContent = newValue;
                countInput.value = newValue;

                // Disable/enable buttons based on limits
                document.querySelector('.counter-btn.minus').disabled = newValue === 1;
                document.querySelector('.counter-btn.plus').disabled = newValue === 10;
            }

            function openDatePicker(inputId) {
                const dateInput = document.createElement('input');
                dateInput.type = 'text'; // Flatpickr üçün sadə input
                dateInput.style.position = 'absolute';
                dateInput.style.zIndex = '9999';
                document.body.appendChild(dateInput);

                flatpickr(dateInput, {
                    minDate: inputId === 'checkOut'
                        ? document.getElementById('checkIn').value || 'today'
                        : 'today',
                    onChange: function(selectedDates, dateStr) {
                        document.getElementById(inputId).value = dateStr;
                        document.getElementById(inputId + 'Text').textContent = dateStr;

                        updateTotalPrice();
                        dateInput.remove(); // Tarix seçildikdən sonra elementi sil
                    }
                });

                dateInput.click(); // Flatpickr-i işə sal
            }


            function updateTotalPrice() {
                const roomRate = parseFloat(document.getElementById('roomRate').textContent.replace('$', ''));
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;

                if (checkIn && checkOut) {
                    const nights = Math.round((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                    document.getElementById('nightCount').textContent = nights;
                    document.getElementById('totalPrice').textContent = '$' + (nights * roomRate).toFixed(2);
                }
            }

            function validateAndCalculate() {
                if (checkInInput.value && checkOutInput.value) {
                    updateTotalPrice();
                }
            }

            checkInInput.addEventListener('change', validateAndCalculate);
            checkOutInput.addEventListener('change', validateAndCalculate);

            // Form submission
            document.getElementById('bookingForm').onsubmit = async function(e) {
                e.preventDefault();
                if (!currentRoom) return;

                const formData = new FormData(this);
                const tg = window.Telegram.WebApp;

                try {
                    const response = await fetch('{{ route('reservations.index') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            room_id: currentRoom.id,
                            check_in: formData.get('checkIn'),
                            check_out: formData.get('checkOut'),
                            guests: formData.get('guests'),
                            client_telegram_id: tg.initDataUnsafe?.user?.id || 'guest_' + Date.now(),
                            client_name: tg.initDataUnsafe?.user?.first_name || 'Guest'
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        closeBookingDialog();
                        if (data.payment_url) {
                            window.location.href = data.payment_url;
                        } else {
                            tg.showPopup({
                                title: 'Success',
                                message: 'Your booking has been confirmed!',
                                buttons: [{type: 'ok'}]
                            });
                        }
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    tg.showPopup({
                        title: 'Error',
                        message: error.message || 'Failed to create booking',
                        buttons: [{type: 'ok'}]
                    });
                }
            };
        </script>
    @endpush
@endsection
