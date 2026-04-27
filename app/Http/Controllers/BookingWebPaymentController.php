<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingWebPaymentController extends Controller
{
    public function __construct(protected BookingService $service)
    {
    }

    /** Pay from a signed payment link (no auth required). */
    public function payByLink(Request $request, Booking $booking)
    {
        // If GET, render human-friendly payment page. If POST, perform payment.
        if ($request->isMethod('get')) {
            if (! $request->hasValidSignature()) {
                return view('bookings.link')->with(['message' => 'Invalid or expired payment link', 'booking' => null]);
            }
            return view('bookings.pay')->with(['booking' => $booking]);
        }

        // POST: perform payment using service
        if (! $request->hasValidSignature()) {
            return view('bookings.link')->with(['message' => 'Invalid or expired payment link', 'booking' => null]);
        }

        try {
            $paid = $this->service->pay($booking);
            // incase request is from say AJAX
            if ($request->expectsJson()) {
                return response()->json($paid->fresh()->load('space'));
            }

            return redirect()->route('bookings.pay.success', ['booking' => $paid->id]);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Throwable $e) {
            Log::error('Payment link error: '.$e->getMessage());
            return view('bookings.link')->with(['message' => $e->getMessage(), 'booking' => null]);
        }
    }

    public function paySuccess(Booking $booking)
    {
        return view('bookings.success')->with([
            'booking' => $booking,
            'message' => 'Payment successful — your booking is confirmed.'
        ]);
    }
}
