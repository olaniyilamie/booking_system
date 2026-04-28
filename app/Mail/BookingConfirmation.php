<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class BookingConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Booking $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $cancelUrl = URL::temporarySignedRoute(
            'bookings.cancel',
            now()->addMinutes(120),
            ['booking' => $this->booking->id]
        );

        $paymentUrl = URL::temporarySignedRoute(
            'bookings.pay',
            now()->addMinutes(120),
            ['booking' => $this->booking->id]
        );

        return $this->subject('Booking confirmation')
                    ->view('emails.booking.confirmation')
                    ->with([
                        'booking' => $this->booking,
                        'cancel_url' => $cancelUrl,
                        'payment_url' => $paymentUrl,
                    ]);
    }
}
