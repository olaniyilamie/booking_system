<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\BookingCancellation;

class BookingController extends Controller
{
    public function __construct(protected BookingService $service)
    {

    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Booking::class);
        $bookings = $this->service->listAll();
        return response()->json(BookingResource::collection($bookings));
    }

    /**
     * Return bookings for a specific authenticated user.
     */
    public function bookingsByUser(User $user): JsonResponse
    {
        $this->authorize('view', $user);
        $bookings = $this->service->listForUser($user);
        return response()->json([
            'status' => 'success',
            'data' => BookingResource::collection($bookings)
        ]);
    }

    /**
     * Return bookings for a specific email (query param `email=`).
     */
    public function bookingsByEmail(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Booking::class);
        $email = $request->query('email');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'Valid email parameter required'], 400);
        }
        $bookings = $this->service->listForEmail($email);
        return response()->json([
            'status' => 'success',
            'data' => BookingResource::collection($bookings)
        ]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        try{
            $booking = $this->service->create($request->validated(), $request->user());
            return response()->json([
                'status' => 'success',
                'message' => 'Booking created successfully, kindly check your email for a confirmation mail and please proceed to payment to confirm your booking.',
                'data' => new BookingResource($booking)
            ], 201);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Throwable $e) {
            Log::error('Booking creation error: '.$e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);
        return response()->json([
            'status' => 'success',
            'data' => new BookingResource($booking)
        ]);
    }

    public function update(UpdateBookingRequest $request, Booking $booking): JsonResponse
    {
        try {
            $this->authorize('update', $booking);
            $updated = $this->service->update($booking, $request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Booking updated successfully, please check your email for confirmation. If you are yet to pay for this booking, kindly proceed to payment to confirm your booking.',
                'data' => new BookingResource($updated)
            ]);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Throwable $e) {
            Log::error('Booking update error: '.$e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $this->authorize('delete', $booking);
        try {
                $deleted = $this->service->delete($booking);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Booking deleted successfully',
                    'data' => $deleted
                ]);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Throwable $e) {
            Log::error('Booking deletion error: '.$e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel a booking — supports signed-link GET cancellations and API POST cancellations
     * API-only cancellation.
     */
    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('cancel', $booking);
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

            return response()->json([
                'status' => 'success',
                'message' => 'Booking cancelled successfully. If this was a mistake, please create a new booking.',
                'data' => new BookingResource($cancelled)
            ]);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
