@php use Carbon\Carbon; @endphp
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <!-- Başlık -->
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Payment Details</h5>
                    </div>

                    <!-- Rezervasyon Bilgileri -->
                    <div class="card-body">
                        <div class="border-bottom pb-3 mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Hotel:</strong></p>
                                    <p class="text-muted">{{ $payment->reservation->room->hotel->name }}</p>

                                    <p class="mb-1"><strong>Room Type:</strong></p>
                                    <p class="text-muted">{{ $payment->reservation->room->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Check-in:</strong></p>
                                    <p class="text-muted">{{ Carbon::parse($payment->reservation->check_in)->format('d M Y') }}</p>

                                    <p class="mb-1"><strong>Check-out:</strong></p>
                                    <p class="text-muted">{{ Carbon::parse($payment->reservation->check_out)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Fiyat Detayları -->
                        <div class="border-bottom pb-3 mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Price per night</span>
                                <span>${{ number_format($payment->reservation->room->price, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Number of nights</span>
                                <span>× {{ Carbon::parse($payment->reservation->check_in)->diffInDays(Carbon::parse($payment->reservation->check_out)) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <strong>Total Amount</strong>
                                <strong class="text-primary">${{ number_format($payment->amount, 2) }}</strong>
                            </div>
                        </div>

                        <!-- Ödeme Formu -->
                        <form id="paymentForm">
                            <input type="hidden" name="payment_id" value="{{ $payment->id }}">

                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="cardNumber" name="card_number"
                                       maxlength="19" placeholder="4111 1111 1111 1111" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry" name="expiry"
                                           placeholder="MM/YY" maxlength="5" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123"
                                           maxlength="3" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Cardholder Name</label>
                                <input type="text" class="form-control" id="cardName" name="card_name"
                                       placeholder="Name on card" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-lg" id="payButton">
                                Pay ${{ number_format($payment->amount, 2) }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tg = window.Telegram.WebApp;
                tg.ready();

                const form = document.getElementById('paymentForm');
                const payButton = document.getElementById('payButton');

                // Kart numarası formatlaması
                document.getElementById('cardNumber').addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = value.replace(/(\d{4})/g, '$1 ').trim();
                    e.target.value = value;
                });

                // Son kullanma tarihi formatlaması
                document.getElementById('expiry').addEventListener('input', function (e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2);
                    }
                    e.target.value = value;
                });

                // CVV sadece rakam
                document.getElementById('cvv').addEventListener('input', function (e) {
                    e.target.value = e.target.value.replace(/\D/g, '');
                });

                // Form gönderimi
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    payButton.disabled = true;
                    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                    try {
                        const formData = new FormData(form);
                        const formObject = {};

                        formData.forEach((value, key) => {
                            // Kart numarasındaki boşlukları temizle
                            if (key === 'card_number') {
                                formObject[key] = value.replace(/\s/g, ''); // Tüm boşlukları kaldır
                            } else {
                                formObject[key] = value;
                            }
                        });

                        const response = await fetch('{{ route('payments.process') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify(formObject)
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('Payment successful!');
                            tg.close();
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        alert(error.message || 'Payment failed. Please try again.');
                        payButton.disabled = false;
                        payButton.innerHTML = 'Pay ${{ number_format($payment->amount, 2) }}';
                    }
                });
            });
        </script>
    @endpush
@endsection
