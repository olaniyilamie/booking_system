<?php

namespace App\Services;

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\Space;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\URL;

class BookingService
{
    /** Create booking while preventing double-booking for same space. */
    public function create(array $data, $user): Booking
    {
        return DB::transaction(function () use ($data, $user) {
            // check overlapping bookings and respect space capacity
            $space = Space::findOrFail($data['space_id']);

            // if guest supplied email that matches existing a user, link it.
            if (!$user && !empty($data['email'])) {
                $found = User::where('email', $data['email'])->first();
                if ($found) {
                    $user = $found;
                }
            }

            // determine if space is seat-based
            $isSeatBased = (bool) ($space->is_seat_based ?? false);

            $start = $data['start_time'];
            $end = $data['end_time'];

            $overlapQuery = Booking::where('space_id', $data['space_id'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end])
                      ->orWhere(function ($q2) use ($start, $end) {
                          $q2->where('start_time', '<=', $start)
                             ->where('end_time', '>=', $end);
                      });
                });

            // Seat-based: ensure capacity isn't already filled and that selected seat is free
            if ($isSeatBased) {
                if (empty($data['seat_number'])) {
                    throw new \Exception('Seat number is required for seat based spaces');
                }

                // If total overlapping (non-cancelled) bookings already occupy all seats, reject
                $occupiedCount = (clone $overlapQuery)->count();
                if ($occupiedCount >= (int) $space->capacity) {
                    throw new \Exception('No seats available for the selected time range');
                }

                $seat = (int) $data['seat_number'];
                if ($seat < 1 || $seat > (int) $space->capacity) {
                    throw new \Exception('Invalid seat number for selected space');
                }

                // Ensure the specific seat isn't already taken
                $seatTaken = (clone $overlapQuery)->where('seat_number', $seat)->exists();
                if ($seatTaken) {
                    throw new \Exception('Selected seat is already booked for the chosen time range');
                }
            } else {
                // Exclusive space: any overlapping (non-cancelled) booking blocks the slot
                if ((clone $overlapQuery)->exists()) {
                    throw new \Exception('Time slot not available for selected space');
                }
            }

            // prevent same user from double-booking the same space at the sametime when user is known
            if ($user) {
                $userHas = (clone $overlapQuery)->where('user_id', $user->id)->exists();
                if ($userHas) {
                    throw new \Exception('You already have a booking for this space in the selected time range');
                }
            } elseif (!empty($data['email'])) {
                // prevent duplicate guest bookings by the same email for overlapping slot
                $email = $data['email'];
                $emailHas = (clone $overlapQuery)->where('email', $email)->exists();
                if ($emailHas) {
                    throw new \Exception('A booking with this email already exists for the selected time range');
                }
            }

            $booking = Booking::create([
                'user_id' => $user?->id,
                'email' => $data['email'] ?? null,
                'space_id' => $data['space_id'],
                'seat_number' => $isSeatBased ? $data['seat_number'] : null,
                'start_time' => $start,
                'end_time' => $end,
                'status' => $data['status'] ?? 'pending',
                'hold_expires_at' => now()->addMinutes(15),
                'paid_at' => null,
            ]);

            // send confirmation email to the booking email (registered or guest)
            $to = $user?->email ?? $booking->email;
            if (!empty($to)) {
                try {
                    Mail::to($to)->send(new BookingConfirmation($booking));
                } catch (\Throwable $e) {
                    Log::error("Failed to send booking confirmation email for booking {$booking->id} to {$to}: {$e->getMessage()}");
                }
            }

            return $booking;
        });
    }

    public function update(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            if (isset($data['start_time']) || isset($data['end_time'])) {
                $start = $data['start_time'] ?? $booking->start_time->toDateTimeString();
                $end = $data['end_time'] ?? $booking->end_time->toDateTimeString();

                $space = $booking->space()->first();

                if ($booking->status == 'cancelled') {
                    throw new \Exception('Cannot update a cancelled booking');
                }

                // If booking is already paid, ensure updated duration does not exceed the originally paid duration.
                if ($booking->paid_at) {
                    $originalPaidMinutes = $booking->end_time->diffInMinutes($booking->start_time);
                    $newMinutes = Carbon::parse($start)->diffInMinutes(Carbon::parse($end));

                    if ($newMinutes > $originalPaidMinutes) {
                        $extra = $newMinutes - $originalPaidMinutes;

                        $formatMinutes = function (int $mins) {
                            if ($mins < 60) {
                                return "{$mins} mins";
                            }
                            $hours = intdiv($mins, 60);
                            $remaining = $mins % 60;
                            if ($remaining === 0) {
                                return "{$hours} hrs";
                            }
                            return "{$hours} hrs {$remaining} mins";
                        };

                        $msg = sprintf(
                            'Please book within the previously paid %s and create a new booking for the extra %s.',
                            $formatMinutes($originalPaidMinutes),
                            $formatMinutes($extra)
                        );

                        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
                            'status' => 'error',
                            'message' => $msg
                        ], 422));
                    }
                }

                $overlapQuery = Booking::where('space_id', $booking->space_id)
                    ->where('id', '!=', $booking->id)
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('start_time', [$start, $end])
                          ->orWhereBetween('end_time', [$start, $end])
                          ->orWhere(function ($q2) use ($start, $end) {
                              $q2->where('start_time', '<=', $start)
                                 ->where('end_time', '>=', $end);
                          });
                    });

                $isSeatBased = (bool) ($space->is_seat_based ?? false);

                if ($isSeatBased && ($data['seat_number'] ?? $booking->seat_number)) {
                    $seat = $data['seat_number'] ?? $booking->seat_number;
                    if (empty($seat)) {
                        throw new \Exception('Seat number is required for seat based space bookings');
                    }
                    $seat = (int) $seat;
                    if ($seat < 1 || $seat > (int) $space->capacity) {                        
                        throw new \Exception('Invalid seat number for selected space');
                    }
                    $overlapQuery->where('seat_number', $seat);

                    if ($overlapQuery->exists()) {
                        throw new \Exception('Selected seat is already booked for the chosen time range');
                    }
                } else {
                    if ($overlapQuery->exists()) {
                        throw new \Exception('Time slot not available for selected space');
                    }
                }

                // prevent same user double-booking when updating (if user known)
                if ($booking->user_id) {
                    $userHas = (clone $overlapQuery)->where('user_id', $booking->user_id)->exists();
                    if ($userHas) {
                        throw new \Exception('You already have a booking for this space in the selected time range');
                    }
                } else {
                    // prevent duplicate guest bookings by email when updating
                    $email = $data['email'] ?? $booking->email;
                    if (!empty($email)) {
                        $emailHas = (clone $overlapQuery)->where('email', $email)->exists();
                        if ($emailHas) {
                            throw new \Exception('A booking with this email already exists for the selected time range');
                        }
                    }
                }

                
            }

            $booking->fill($data);
            if (!$booking->paid_at) {
                $booking->hold_expires_at = now()->addMinutes(15);
            }
            $booking->save();
            
            return $booking;
        });
    }

    public function cancel(Booking $booking)
    {
        if ($booking->paid_at) {
            throw new \Exception('Cannot cancel a paid booking');        
        }

        if ($booking->status == 'cancelled') {
            throw new \Exception('This booking has already been cancelled');
        }

        if ($booking->status !== 'pending') {
            throw new \Exception('This booking cannot be cancelled.');
        }

        $booking->status = 'cancelled';
        $booking->hold_expires_at = null;
        $booking->save();
        
        return $booking;
    }

    public function delete(Booking $booking)
    {
        // Do not allow deletion of paid bookings
        if ($booking->paid_at) {
            throw new \Exception('Cannot delete a paid booking');
        }

        // Allow deletion only if the booking period has passed or it's already cancelled
        $now = Carbon::now();
        $periodPassed = Carbon::parse($booking->end_time)->lte($now);

        if (! $periodPassed && $booking->status !== 'cancelled') {
            throw new \Exception('Booking can only be deleted after the booking period has passed or if it is cancelled');
        }

        // Perform hard delete
        $bookingId = $booking->id;
        $booking->delete();

        return ['deleted' => true, 'id' => $bookingId];
    }

    /**
     * List bookings for an authenticated user.
     */
    public function listForUser(User $user)
    {
        return Booking::where('user_id', $user->id)->with('space')->orderBy('start_time')->get();
    }

    /**
     * List all bookings (for admin or overview).
     */
    public function listAll()
    {
        return Booking::with('space')->orderBy('start_time')->get();
    }

    /**
     * List bookings by email.
     */
    public function listForEmail(string $email)
    {
        return Booking::where('email', $email)->with('space')->orderBy('start_time')->get();
    }

    /** Mark a booking as paid and confirm it. */
    public function pay(Booking $booking): Booking
    {
        if ($booking->status == 'cancelled') {
            throw new \Exception('Cannot pay for a cancelled booking');
        }

        if ($booking->paid_at) {
            throw new \Exception('Booking is already paid');
        }

        $booking->paid_at = now();
        $booking->status = 'confirmed';
        $booking->hold_expires_at = null;
        $booking->save();

        // send confirmation email
        $to = $booking->email ?? $booking->user?->email;
        if (!empty($to)) {
            try {
                Mail::to($to)->send(new BookingConfirmation($booking));
            } catch (\Throwable $e) {
                Log::error("Failed to send booking paid confirmation for booking {$booking->id}: {$e->getMessage()}");
            }
        }

        return $booking;
    }

    /** Generate a temporary signed payment link for a booking. */
    public function paymentLink(Booking $booking, int $minutes = 60): string
    {
        return URL::temporarySignedRoute('bookings.pay', now()->addMinutes($minutes), ['booking' => $booking->id]);
    }
}
