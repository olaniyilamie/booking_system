<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invalid Link</title>
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
      text-align: center;
    }

    h1 {
      margin-top: 0;
      color: #d97706; /* amber warning tone */
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
      text-align: left;
    }

    .details p {
      margin: 6px 0;
    }

    .actions {
      margin-top: 20px;
    }

    .btn {
      display: inline-block;
      padding: 10px 18px;
      background: #3b82f6;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-size: 14px;
    }

    .btn:hover {
      background: #2563eb;
    }

    .icon {
      font-size: 40px;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="card">

      <div class="icon">⚠️</div>
      <h1>Link Issue</h1>

      <p>{{ $message ?? 'The link is invalid or has expired.' }}</p>

      @if($booking)
        <div class="details">
          <p><strong>Booking ID:</strong> {{ $booking->id }}</p>
          <p><strong>Space:</strong> {{ $booking->space?->name ?? 'N/A' }}</p>
          <p><strong>Start:</strong> {{ $booking->start_time }}</p>
          <p><strong>End:</strong> {{ $booking->end_time }}</p>
        </div>
      @endif

      <div class="actions">
        <a href="/" class="btn">Return to site</a>
      </div>

    </div>
  </div>

</body>
</html>