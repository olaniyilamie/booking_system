<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\API\BookingPaymentController;
use App\Http\Controllers\API\SpaceController;


// Bookings (authenticated)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('spaces', [SpaceController::class, 'index']);
Route::get('spaces/{space}', [SpaceController::class, 'show']);
Route::prefix('bookings')->group(function () {
    // Allow guest bookings via POST; other booking actions require auth
    Route::post('/', [BookingController::class, 'store'])->middleware('throttle:booking-actions');

    Route::middleware(['auth:sanctum', 'token.not.expired', 'throttle:auth-bookings'])->group(function () {
        // Authenticated API routes
        Route::get('/', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('/user/{user}', [BookingController::class, 'bookingsByUser']);
        Route::get('/by-email', [BookingController::class, 'bookingsByEmail']);
        Route::put('/{booking}', [BookingController::class, 'update']);
        Route::delete('/{booking}', [BookingController::class, 'destroy']);

        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel.api')->middleware('throttle:booking-actions');
        Route::post('/{booking}/pay', [BookingPaymentController::class, 'pay']);
    });
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'token.not.expired'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
