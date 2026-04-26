<div style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, sans-serif;">
    <div style="max-width:600px; margin:40px auto; padding:20px;">
        
        <!-- Card -->
        <div style="background:#ffffff; border-radius:10px; padding:25px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
            
             @if($booking->paid_at)
                <h2 style="margin-top:0; color:#333;">Payment Confirmation</h2>
            @else
                <h2 style="margin-top:0; color:#333;">Booking Confirmation</h2>
            @endif

            <p style="color:#555;">Hi,</p>
            @if($booking->paid_at)
                <p style="color:#555;">Your payment has been received. Thank you!</p>
            @else
                <p style="color:#555;">Your booking has been received. Here are the details:</p>
            @endif
            <div style="background:#f9fafb; border-radius:8px; padding:15px; margin:20px 0;">
                <p style="margin:6px 0;"><strong>Space:</strong> {{ $booking->space?->name ?? 'N/A' }}</p>
                
                @if(!empty($booking->seat_number))
                <p style="margin:6px 0;"><strong>Seat:</strong> {{ $booking->seat_number }}</p>
                @endif
                
                <p style="margin:6px 0;"><strong>Start:</strong> {{ $booking->start_time }}</p>
                <p style="margin:6px 0;"><strong>End:</strong> {{ $booking->end_time }}</p>
                <p style="margin:6px 0;"><strong>Status:</strong> {{ $booking->status }}</p>
            </div>

            @if(empty($booking->paid_at) && !empty($cancel_url))
                <p style="color:#555;">If you need to cancel, you can use this link (valid for 60 minutes):</p>
                
                <p style="text-align:center;">
                    <a href="{{ $cancel_url }}" 
                       style="display:inline-block; padding:10px 18px; background:#ef4444; color:#ffffff; text-decoration:none; border-radius:6px;">
                        Cancel Booking
                    </a>
                </p>

                @if(!empty($payment_url))
                    <p style="color:#555;">To complete your booking, pay using this secure link (valid for 60 minutes):</p>
                    
                    <p style="text-align:center;">
                        <a href="{{ $payment_url }}" 
                           style="display:inline-block; padding:10px 18px; background:#10b981; color:#ffffff; text-decoration:none; border-radius:6px;">
                            Pay for Booking
                        </a>
                    </p>
                @endif
            @endif

            <p style="margin-top:25px; color:#555;">Thank you once again for trusting us with your convenience.</p>
        </div>

    </div>
</div>