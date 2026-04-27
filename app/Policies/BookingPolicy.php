<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /** Allow owners or super_admin to view a booking. */
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'super_admin';
    }

    /** Allow owners or super_admin to update. */
    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'super_admin';
    }

    /** Allow owners or super_admin to delete/cancel. */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'super_admin';
    }

    /** Only super_admin can view all bookings. */
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    /**  Allow owners or super_admin to pay for a booking. */
    public function pay(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'super_admin';
    }

    /** Only super_admin can delete bookings. */
    public function delete(User $user): bool
    {
        return $user->role === 'super_admin';
    }
}
