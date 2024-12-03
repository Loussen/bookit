// public/js/booking.js
class BookingManager {
    constructor() {
        this.tg = window.Telegram.WebApp;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        document.querySelectorAll('.book-room').forEach(button => {
            button.addEventListener('click', (e) => this.handleBookClick(e));
        });
    }

    async handleBookClick(e) {
        const roomId = e.target.dataset.roomId;
        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));

        // Reset form
        document.getElementById('bookingForm').reset();

        // Show modal
        modal.show();

        // Handle date changes
        const checkInInput = document.querySelector('[name="check_in"]');
        const checkOutInput = document.querySelector('[name="check_out"]');

        [checkInInput, checkOutInput].forEach(input => {
            input.addEventListener('change', () => this.checkAvailability(roomId));
        });

        // Handle booking confirmation
        document.getElementById('confirmBooking').onclick = () => this.processBooking(roomId);
    }

    async checkAvailability(roomId) {
        const formData = new FormData(document.getElementById('bookingForm'));

        try {
            const response = await fetch('/check-availability', {
                method: 'POST',
                body: JSON.stringify({
                    room_id: roomId,
                    check_in: formData.get('check_in'),
                    check_out: formData.get('check_out')
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (!data.available) {
                alert('Room is not available for selected dates');
                return false;
            }

            document.getElementById('totalPrice').textContent = `Total: $${data.price}`;
            return true;

        } catch (error) {
            console.error('Error checking availability:', error);
            return false;
        }
    }

    async processBooking(roomId) {
        const formData = new FormData(document.getElementById('bookingForm'));

        try {
            const response = await fetch('/reservations', {
                method: 'POST',
                body: JSON.stringify({
                    room_id: roomId,
                    check_in: formData.get('check_in'),
                    check_out: formData.get('check_out'),
                    guest_name: this.tg.initDataUnsafe.user.first_name,
                    guest_phone: formData.get('phone'),
                    guest_telegram_id: this.tg.initDataUnsafe.user.id
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Redirect to payment page
                window.location.href = data.payment_url;
            } else {
                alert(data.message);
            }

        } catch (error) {
            console.error('Error processing booking:', error);
            alert('An error occurred while processing your booking');
        }
    }
}

// Initialize booking manager
new BookingManager();
