<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('hotels', 'HotelsCrudController');
    Route::crud('rooms', 'RoomsCrudController');
    Route::crud('room-photos', 'RoomPhotosCrudController');
    Route::crud('reservations', 'ReservationsCrudController');
    Route::crud('admins', 'AdminsCrudController');
    Route::crud('room-unavailable-dates', 'RoomUnavailableDatesCrudController');
    Route::crud('payments', 'PaymentsCrudController');
    Route::crud('payment-histories', 'PaymentHistoriesCrudController');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
