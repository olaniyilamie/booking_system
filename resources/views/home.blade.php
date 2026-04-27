<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Home — Booking System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin:0;
      font-family: Inter, Arial, sans-serif;
      background:#f6f7fb;
      color:#1e293b;
    }

    .container {
      max-width:1100px;
      margin:40px auto;
      padding:24px;
    }

    .card {
      background:#fff;
      border-radius:14px;
      padding:32px;
      box-shadow:0 10px 30px rgba(0,0,0,0.06);
    }

    /* HERO */
    .hero {
      display:flex;
      gap:24px;
      align-items:center;
      border-bottom:1px solid #eef2f7;
      padding-bottom:24px;
    }

    .logo {
      font-weight:700;
      font-size:18px;
      color:#0f172a;
    }

    .hero h1 {
      margin:0;
      font-size:32px;
      font-weight:700;
    }

    .lead {
      color:#475569;
      margin-top:6px;
      font-size:15px;
    }

    .btn {
      display:inline-block;
      padding:10px 16px;
      border-radius:8px;
      text-decoration:none;
      font-size:14px;
      font-weight:500;
      transition:0.2s ease;
    }

    .btn-primary {
      background:#26ABB2;
      color:#fff;
      cursor: pointer;
    }

    .btn-primary:hover {
      background:#1d848a;
    }

    .btn-secondary {
      background:#e2e8f0;
      color:#1e293b;
    }

    /* GRID */
    .grid {
      display:grid;
      grid-template-columns:repeat(auto-fit, minmax(250px,1fr));
      gap:18px;
      margin-top:28px;
    }

    .tile {
      background:#ffffff;
      padding:20px;
      border-radius:12px;
      border:1px solid #eef2f7;
      transition:0.25s ease;
    }

    .tile:hover {
      transform:translateY(-4px);
      box-shadow:0 12px 24px rgba(0,0,0,0.08);
    }

    .tile h3 {
      margin:0 0 6px 0;
      font-size:16px;
    }

    .muted {
      color:#64748b;
      font-size:14px;
      line-height:1.5;
    }

    /* ROADMAP */
    .section-title {
      margin-top:36px;
      font-size:18px;
      font-weight:600;
    }

    .roadmap {
      margin-top:16px;
      display:grid;
      gap:12px;
    }

    .roadmap-item {
      padding:14px;
      border-radius:10px;
      background:#f8fafc;
      border:1px solid #e2e8f0;
      font-size:14px;
    }

    footer {
      margin-top:32px;
      color:#94a3b8;
      font-size:13px;
      text-align:center;
    }

  </style>
</head>
<body>

  <div class="container">
    <div class="card">

      @if(session('error'))
        <div style="background:#fee2e2;color:#b91c1c;padding:12px;border-radius:8px;margin-bottom:16px">{{ session('error') }}</div>
      @endif
      @if(session('message'))
        <div style="background:#eef2ff;color:#1e3a8a;padding:12px;border-radius:8px;margin-bottom:16px">{{ session('message') }}</div>
      @endif

      <!-- HERO -->
      <header class="hero">
        <div class="logo">
          <img src="{{ asset('images/logo.png') }}" alt="Booking System" style="height:80px; display:block" />
        </div>

        <div style="flex:1">
          <h1>Booking System</h1>
          <p class="lead">
            Manage bookings, temporary holds, and payments with a scalable Laravel backend.
          </p>
        </div>

        <div>
          <a class="btn btn-primary" href="/bookings">Make Booking</a>
        </div>
      </header>

      <!-- FEATURES -->
      <div class="grid">
        <div class="tile">
          <h3>My Bookings</h3>
          <p class="muted">View, update, cancel, or pay for your bookings.</p>
        </div>

        <div class="tile">
          <h3>Spaces & Seats</h3>
          <p class="muted">Book seat-based rooms or exclusive spaces without overlap.</p>
        </div>

        <div class="tile">
          <h3>Secure Payments</h3>
          <p class="muted">Simulated payment flow with signed email links or API.</p>
        </div>

        <div class="tile">
          <h3>Temporary Holds</h3>
          <p class="muted">Bookings are reserved for 15 minutes before expiry.</p>
        </div>

        <div class="tile">
          <h3>Email Workflows</h3>
          <p class="muted">Pay or cancel via secure, time-limited links.</p>
        </div>

        <div class="tile">
          <h3>API Ready</h3>
          <p class="muted">Designed for SPA and external integrations.</p>
        </div>
      </div>

      <!-- ROADMAP -->
      <h2 class="section-title">🚧 Coming Next</h2>

      <div class="roadmap">
        <div class="roadmap-item">Frontend UI (Blade + Alpine)</div>
        <div class="roadmap-item">Queued email system for faster and reliable delivery</div>
        <div class="roadmap-item">Background jobs for booking expiration and retries</div>
        <div class="roadmap-item">Email verification for user accounts</div>
      </div>

      <!-- FOOTER -->
      <footer>
        Need API access? See the README for curl and Postman examples.
      </footer>

    </div>
  </div>

</body>
</html>