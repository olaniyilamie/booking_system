<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BookingPaymentController extends Controller
{
    public function __construct(protected BookingService $service)
    {
    }

    /**
     * Fake payment endpoint. Authenticated users can pay directly.
     */
    public function pay(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('pay', $booking);

        try {
            $paid = $this->service->pay($booking);
            return response()->json([
                'status' => 'success',
                'data' => $paid->fresh()->load('space')
            ]);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Throwable $e) {
            Log::error('Payment error: '.$e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
}
