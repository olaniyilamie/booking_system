<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingWebController;
use App\Http\Controllers\BookingWebPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Human-facing routes for signed email links are registered here so they
| use the `web` middleware (sessions, CSRF, redirects, flash messages).
|
*/

// Default home route
Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', function () {
    return view('home');
})->name('home');

// Placeholder frontend route until UI is implemented — shows flash message
Route::get('/bookings', function () {
    session()->flash('error', 'Bookings are currently implemented with API-only. Use the API to create bookings. Frontend booking UI coming soon.');
    return redirect()->route('home');
})->name('bookings.placeholder');

// Signed-link human flows (GET shows page; POST performs action)
Route::prefix('bookings')->group(function () {
    Route::get('/{booking}/cancel', [BookingWebController::class, 'cancelByLink'])->name('bookings.cancel');
    Route::post('/{booking}/cancel', [BookingWebController::class, 'cancelByLink'])->name('bookings.cancel.link');

    Route::get('/{booking}/pay', [BookingWebPaymentController::class, 'payByLink'])->name('bookings.pay');
    Route::post('/{booking}/pay', [BookingWebPaymentController::class, 'payByLink'])->name('bookings.pay.link');

    Route::get('/{booking}/cancel/success', [BookingWebController::class, 'cancelSuccess'])->name('bookings.cancel.success');
    Route::get('/{booking}/pay/success', [BookingWebPaymentController::class, 'paySuccess'])->name('bookings.pay.success');
});