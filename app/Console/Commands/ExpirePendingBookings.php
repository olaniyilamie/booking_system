<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class ExpirePendingBookings extends Command
{
    protected $signature = 'bookings:expire-pending';
    protected $description = 'Expire pending bookings whose hold_expires_at has passed';

    public function handle(): int
    {
        $now = now();
        $expired = Booking::where('status', 'pending')
            ->whereNotNull('hold_expires_at')
            ->where('hold_expires_at', '<', $now)
            ->get();

        foreach ($expired as $b) {
            $b->status = 'cancelled';
            $b->save();
        }

        $this->info('Canceled '.count($expired).' unpaid pending bookings');
        return 0;
    }
}
