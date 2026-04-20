<p>Hi,</p>
<p>Your booking has been received:</p>
<ul>
    <li>Space: {{ $booking->space?->name ?? 'N/A' }}</li>
    @if(!empty($booking->seat_number))
    <li>Seat: {{ $booking->seat_number }}</li>
    @endif
    <li>Start: {{ $booking->start_time }}</li>
    <li>End: {{ $booking->end_time }}</li>
    <li>Status: {{ $booking->status }}</li>
</ul>
@if(empty($booking->paid_at) && !empty($cancel_url))
<p>If you need to cancel, you can use this link (valid for 60 minutes):</p>
<p><a href="{{ $cancel_url }}">Cancel booking</a></p>
@else
<p>If you need to cancel, use your account or contact support.</p>
@endif
<p>Thanks.</p>
