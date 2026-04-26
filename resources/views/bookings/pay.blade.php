<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pay for booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    body {
      margin: 0;
      font-family: Arial, Helvetica, sans-serif;
      background: #f4f6f8;
      color: #333;
    }

    .container {
      max-width: 600px;
      margin: 60px auto;
      padding: 20px;
    }

    .card {
      background: #fff;
      border-radius: 12px;
      padding: 28px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    h1 {
      margin-top: 0;
      color: #1976d2;
      font-size: 24px;
    }

    p {
      color: #555;
      line-height: 1.5;
    }

    .details {
      background: #f9fafb;
      padding: 16px;
      border-radius: 8px;
      margin: 20px 0;
    }

    .details p {
      margin: 6px 0;
    }

    .actions {
      margin-top: 20px;
      text-align: center;
    }

    button {
      background: #1976d2;
      color: #fff;
      border: none;
      padding: 12px 18px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.2s ease;
    }

    button:hover {
      background: #125ea9;
    }

    .secondary {
      display: inline-block;
      margin-top: 12px;
      font-size: 13px;
      color: #666;
      text-decoration: none;
    }

    .secondary:hover {
      text-decoration: underline;
    }

    .note {
      font-size: 13px;
      color: #888;
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="card">

      <h1>Complete Payment</h1>
      <p>This is a demo payment page. Review your booking details before confirming.</p>

      <div class="details">
        <p><strong>Space:</strong> {{ $booking->space?->name ?? 'N/A' }}</p>
        @if($booking->seat_number)
            <p><strong>Seat:</strong> {{ $booking->seat_number}}</p>
        @endif
        <p><strong>Start:</strong> {{ $booking->start_time }}</p>
        <p><strong>End:</strong> {{ $booking->end_time }}</p>
        <p><strong>Status:</strong> {{ $booking->status }}</p>
      </div>

      <form method="POST" action="{{ route('bookings.pay.link', ['booking' => $booking->id, 'expires' => request()->query('expires'), 'signature' => request()->query('signature')]) }}" class="actions">
        @csrf
        <button type="submit">Confirm Payment</button>
      </form>

      <div class="actions">
        <a href="{{ url('/') }}" class="secondary">Cancel and go back</a>
      </div>

      <p class="note">Secure payment link • May expire after a limited time</p>

    </div>
  </div>

</body>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form.actions').forEach(function (form) {
      form.addEventListener('submit', function () {
        var btn = form.querySelector('button[type="submit"]');
        if (!btn) return;
        btn.disabled = true;
        btn.setAttribute('aria-busy', 'true');
        btn.dataset.originalText = btn.innerHTML;
        btn.innerHTML = 'Processing...';
      });
    });
  });
</script>
</html>