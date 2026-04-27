<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCancellation extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public string $messageBody;

    public function __construct(Booking $booking, string $messageBody)
    {
        $this->booking = $booking;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this->subject('Your booking was cancelled')
            ->view('emails.booking.cancelled')
            ->with([
                'booking' => $this->booking,
                'messageBody' => $this->messageBody,
            ]);
    }
}
