@extends('layouts.app')

@section('title', $hotel->name)

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Hotels
            </a>

            <div class="card">
                <div id="hotelCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="{{ $hotel->photo_url }}" class="d-block w-100" alt="{{ $hotel->name }}" style="height: 300px; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h2>{{ $hotel->name }}</h2>
                    <p class="text-muted">
                        <i class="fas fa-map-marker-alt"></i> {{ $hotel->address }}
                        <span class="ms-3">
                        <i class="fas fa-star text-warning"></i> {{ $hotel->rating }}
                    </span>
                    </p>
                    <p>{{ $hotel->description }}</p>
                </div>
            </div>
        </div>

        <h3 class="mb-4">Available Rooms</h3>
        <div class="row">
            @foreach($hotel->rooms as $room)
                <div class="col-12 mb-4">
                    <div class="card room-card">
                        <div class="row g-0">
                            <div class="col-md-4">
                                @if($room->photos->count() > 0)
                                    <img src="{{ $room->photos->first()->photo_url }}"
                                         class="img-fluid rounded-start"
                                         alt="{{ $room->name }}"
                                         style="height: 100%; object-fit: cover;">
                                @endif
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $room->name }}</h5>
                                    <p class="card-text">{{ $room->description }}</p>
                                    <div class="room-amenities mb-3">
                                        <span><i class="fas fa-user"></i> {{ $room->capacity }} Guests</span>
                                        <span><i class="fas fa-bed"></i> {{ $room->bed_type }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="price">
                                            <strong class="text-primary">${{ number_format($room->price, 2) }}</strong>
                                            <small class="text-muted">/ night</small>
                                        </div>
                                        @if($room->is_available)
                                            <button class="btn btn-success book-room"
                                                    data-room-id="{{ $room->id }}"
                                                    data-price="{{ $room->price }}">
                                                Book Now
                                            </button>
                                        @else
                                            <button class="btn btn-secondary" disabled>Not Available</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm">
                        <div class="mb-3">
                            <label class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" name="check_in" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" name="check_out" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of Guests</label>
                            <input type="number" class="form-control" name="guests" min="1" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmBooking">Confirm Booking</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tg = window.Telegram.WebApp;
                tg.ready();

                // Book Now butonlarına click event listener ekle
                document.querySelectorAll('.book-room').forEach(button => {
                    button.addEventListener('click', function() {
                        const roomId = this.dataset.roomId;
                        const price = this.dataset.price;

                        // Booking modal'ı göster
                        const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                        bookingModal.show();

                        // Confirm Booking butonuna click event listener ekle
                        document.getElementById('confirmBooking').onclick = async function() {
                            const form = document.getElementById('bookingForm');
                            const formData = new FormData(form);

                            const telegramUser = tg.initDataUnsafe?.user || {};
                            const defaultClientId = 'guest_' + Math.random().toString(36).substring(7);
                            const defaultName = 'Guest User';

                            if (!form.checkValidity()) {
                                form.reportValidity();
                                return;
                            }

                            try {
                                const response = await fetch('{{ route('reservations.index') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    },
                                    body: JSON.stringify({
                                        room_id: roomId,
                                        check_in: formData.get('check_in'),
                                        check_out: formData.get('check_out'),
                                        guests: formData.get('guests'),
                                        client_telegram_id: telegramUser.id?.toString() || defaultClientId,
                                        client_name: telegramUser.first_name
                                            ? `${telegramUser.first_name} ${telegramUser.last_name || ''}`
                                            : defaultName
                                    })
                                });

                                const data = await response.json();

                                if (response.ok && data.success) {
                                    bookingModal.hide();

                                    if (data.payment_url) {
                                        window.location.href = data.payment_url;
                                    } else {
                                        alert('Reservation created successfully!');
                                        tg.close();
                                    }
                                } else {
                                    throw new Error(data.message || 'An error occurred');
                                }
                            } catch (error) {
                                alert(error.message || 'Failed to create reservation');
                            }
                        };
                    });
                });

                // Tarih inputlarına minimum tarih sınırlaması ekle
                const today = new Date().toISOString().split('T')[0];
                document.querySelector('input[name="check_in"]').min = today;
                document.querySelector('input[name="check_out"]').min = today;

                // Check-in tarihi değiştiğinde check-out minimum tarihini güncelle
                document.querySelector('input[name="check_in"]').addEventListener('change', function() {
                    const checkOutInput = document.querySelector('input[name="check_out"]');
                    checkOutInput.min = this.value;
                    if (checkOutInput.value && checkOutInput.value < this.value) {
                        checkOutInput.value = this.value;
                    }
                });
            });
        </script>
    @endpush
@endsection
