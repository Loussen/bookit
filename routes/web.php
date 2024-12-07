<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    HotelController,
    ReservationController,
    PaymentController
};

Route::get('/', [HotelController::class, 'index'])->name('hotels.index');

Route::prefix('hotels')->name('hotels.')->group(function () {
    Route::get('/{hotel}', [HotelController::class, 'show'])->name('show');
    Route::post('/{hotel}/check-availability', [HotelController::class, 'checkAvailability'])->name('check-availability');
});

Route::prefix('reservations')->name('reservations.')->group(function () {
    Route::post('/', [ReservationController::class, 'store'])->name('index');
    Route::get('/my/{clientTelegramId}', [ReservationController::class, 'myReservations'])->name('my-reservations');
    Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
    Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
});

Route::prefix('payments')->name('payments.')->group(function () {
    Route::post('/process', [PaymentController::class, 'process'])->name('process');
    Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
    Route::get('/{payment}/status', [PaymentController::class, 'status'])->name('status');
    Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
});
