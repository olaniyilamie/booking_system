<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\BookingCancellation;

class BookingWebController extends Controller
{
    public function __construct(protected BookingService $service)
    {
    }

    /** Signed-link cancel flow for human-facing pages. */
    public function cancelByLink(Request $request, Booking $booking)
    {
        $isSignedLink = $request->hasValidSignature();

        if ($request->isMethod('get')) {
            if (! $isSignedLink) {
                return view('bookings.link')->with(['message' => 'Invalid or expired link', 'booking' => null]);
            }

            return view('bookings.cancel')->with(['booking' => $booking, 'action' => route('bookings.cancel.link', ['booking' => $booking->id, 'expires' => request()->query('expires'), 'signature' => request()->query('signature')])]);
        }

        if (! $isSignedLink) {
            return view('bookings.link')->with(['message' => 'Invalid or expired link', 'booking' => null]);
        }

        try {
            $cancelled = $this->service->cancel($booking);

            $to = $booking->email ?? $booking->user?->email;
            if (!empty($to)) {
                try {
                    Mail::to($to)->send(new BookingCancellation($cancelled, 'Your booking was successfully cancelled.'));
                } catch (\Throwable $e) {
                    Log::error('Failed to send cancellation email for booking '.$cancelled->id.': '.$e->getMessage());
                }
            }

            return redirect()->route('bookings.cancel.success', ['booking' => $cancelled->id])->with('message', 'Booking cancelled successfully.');
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Throwable $e) {
            Log::error('Cancellation error: '.$e->getMessage());
            return view('bookings.link')->with(['message' => $e->getMessage(), 'booking' => null]);
        }
    }

    public function cancelSuccess(Booking $booking)
    {
        return view('bookings.success')->with([
            'booking' => $booking,
            'message' => 'Your booking has been successfully cancelled.'
        ]);
    }
}
