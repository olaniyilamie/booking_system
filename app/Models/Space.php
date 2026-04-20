<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Space extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'hourly_rate',
        'is_seat_based',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    protected static bool $seatFlagApplied = false;

    /**
     * Run once when the model is booted for the request lifecycle.
     */
    protected static function booted(): void
    {
        if (static::$seatFlagApplied) {
            return;
        }

        if (!Schema::hasTable('spaces')) {
            return;
        }

        try {
            \App\Models\Space::where('name', 'Hot Desk')->update(['is_seat_based' => true]);
        } catch (\Throwable $e) {
            Log::warning('Could not apply is_seat_based flag to Hot Desk space: ' . $e->getMessage());
        }

        static::$seatFlagApplied = true;
    }
}
