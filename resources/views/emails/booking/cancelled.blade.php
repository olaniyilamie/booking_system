<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Booking Cancelled</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif; color:#333;">

  <div style="max-width:600px; margin:40px auto; padding:20px;">
    
    <!-- Card -->
    <div style="background:#ffffff; border-radius:10px; padding:25px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
      
      <h2 style="margin-top:0; color:#ef4444;">Booking Cancelled</h2>

      <p style="color:#555;">{{ $messageBody }}</p>

      <!-- Booking Details -->
      <div style="background:#f9fafb; border-radius:8px; padding:15px; margin:20px 0;">
        <p style="margin:6px 0;"><strong>Space:</strong> {{ $booking->space->name ?? 'N/A' }}</p>
        <p style="margin:6px 0;"><strong>Start:</strong> {{ $booking->start_time }}</p>
        <p style="margin:6px 0;"><strong>End:</strong> {{ $booking->end_time }}</p>
        @if($booking->seat_number)
            <p style="margin:6px 0;"><strong>Seat:</strong> {{ $booking->seat_number}}</p>
        @endif
      </div>

      <p style="color:#555;">
        If this was a mistake, you can create a new booking anytime.
      </p>

      <p style="margin-top:25px; color:#777;">Thanks.</p>

    </div>

  </div>

</body>
</html>