
---

# Booking System (Laravel API)

The **booking system** supporting guest bookings, seat-based and exclusive-space reservations, signed email workflows, temporary holds, and simulated payments for testing.

The system is designed as a **backend-first architecture**, where the API serves as the core application layer and can be consumed by any frontend (Blade).

---

## ⚙️ Tech Stack

* Laravel 10
* PHP 8.1+
* Laravel Sanctum (API authentication)
* MySQL / PostgreSQL / SQLite
* Blade (for signed-link human flows)
* Mailables (email system)
* Laravel Scheduler & Queues (planned expansion)

---

## 🚀 Quick Setup

### 1. Clone & install dependencies

```bash
composer install
cp .env.example .env
php artisan key:generate
```

---

### 2. Configure environment

Update `.env`:

```env
APP_URL=http://localhost:8000
DB_DATABASE=...
```

---

### 3. Run migrations & seed data

```bash
php artisan migrate --seed
```

---

### 4. Start development server

```bash
php artisan serve
```

---

## ⏱ Background Commands

Expire unpaid bookings:

```bash
php artisan bookings:expire-pending
```

---

## 🧠 Core Features

### 👤 Guest & Authenticated Bookings

* Guests can create bookings using email only
* Authenticated users manage their own bookings
* Guest bookings can later be linked to accounts via email

---

### 🪑 Seat-based & Exclusive Spaces

* Seat-based spaces prevent per-seat overlap
* Exclusive spaces prevent any overlapping bookings

---

### ⏳ Temporary Holds

* Unpaid bookings are held for **15 minutes**
* Expired holds are automatically released

---

### 💳 Payment Simulation

* Fake payment system for testing flows
* Supports:

  * API-based payment
  * Signed-link payment (email flow)

---

### ✉️ Email & Signed Links

* Booking confirmation emails include:
  * Payment link
  * Cancellation link

* Secure signed URLs:
  * Time-limited
  * Tamper-proof

* Supports both:
  * JSON API flows
  * Human-friendly Blade flows

---

## 🔐 Authentication & Authorization

### 🔑 Authentication (Sanctum)

All protected routes use:

```php
auth:sanctum
```

---

### 🛡️ Authorization Design

Authorization is implemented using **Laravel Policies + Form Requests**, ensuring a clean separation of concerns.

#### Policy Example

```php
public function update(User $user, Booking $booking)
{
    return $user->id === $booking->user_id
        || $user->role === User::ROLE_SUPER_ADMIN;
}
```

---

### 🧾 Form Request Authorization

Authorization is enforced before reaching controllers:

```php
public function authorize(): bool
{
    $booking = $this->route('booking');

    return $this->user()->can('update', $booking);
}
```

---

### 🔄 Route Model Binding

```php
Route::put('/bookings/{booking}', ...);
```

Automatically resolves:

```
/bookings/5 → Booking::findOrFail(5)
```

---

### 📌 Design Benefits

* Centralized authorization logic (Policies)
* Clean controllers (no inline permission checks)
* Secure-by-default API
* Fully scalable role system
* Ready for frontend integration (SPA/mobile/web)

---

## 🧩 Architecture Overview

### Core Layers

* **Controllers** → Handle HTTP requests only
* **Services** → Business logic (BookingService)
* **Policies** → Authorization rules
* **Form Requests** → Validation + authorization
* **Mailables** → Email system
* **Blade Views** → Signed-link user flows
* **Commands/Jobs** → Background processing (expanding)

---

## 📂 Key Files

* `app/Services/BookingService.php` → Core booking logic
* `app/Http/Controllers/API/BookingController.php` → Booking API
* `app/Http/Controllers/API/BookingPaymentController.php` → Payment logic
* `app/Policies/BookingPolicy.php` → Authorization rules
* `app/Http/Requests/*` → Validation + authorization
* `app/Mail/*` → Email notifications
* `resources/views/bookings/*` → Signed-link UI pages

---

## 🌐 API Routes (JSON)

Base: `/api/bookings`

### CRUD

* `GET /` → List bookings
* `POST /` → Create booking
* `GET /{booking}` → Show booking
* `PUT /{booking}` → Update booking
* `DELETE /{booking}` → Delete booking

---

### Actions

* `POST /{booking}/cancel-api`
* `POST /{booking}/pay`

---

## 🔗 Signed Link Routes (Web)

Used in emails (human-friendly flows):

* `GET /bookings/{booking}/pay`
* `POST /bookings/{booking}/pay`
* `GET /bookings/{booking}/cancel`
* `POST /bookings/{booking}/cancel`

---

## 🧪 Example Request

```bash
curl -X POST http://localhost:8000/api/bookings \
-H "Content-Type: application/json" \
-d '{
  "space_id": 1,
  "start_time": "2026-05-01 10:00:00",
  "end_time": "2026-05-01 11:00:00",
  "email": "guest@example.com"
}'
```

---

## 🚧 Roadmap / Future Improvements

This project is designed as an evolving backend system with production-grade enhancements planned.

---

### 🎨 Frontend (API Consumer)

A frontend will be added later to consume this API:

* Vue / React SPA or Blade + Alpine.js
* Fully API-driven interface
* Token-based authentication via Sanctum
* Booking management UI for users and admins

---

### 📧 Queued Email System

Email delivery will move to queues:

* Booking confirmations
* Cancellation notifications
* Payment confirmations

Benefits:

* Faster API responses
* Reliable background processing
* Scalable architecture

---

### ⚙️ Background Jobs & Scheduler

Planned jobs:

* Expire unpaid bookings (queued version)
* Clear expired Sanctum tokens
* Retry failed email deliveries

Scheduled via Laravel Scheduler.

---

### 🔐 Security Enhancements

* Token cleanup job for expired tokens
* Improved token lifecycle management
* Optional refresh-token pattern (future)

---

### ✉️ Email Verification System

* Email verification on registration
* Signed verification links
* Expiry-based tokens
* Restriction of sensitive actions until verified

---

### 👤 Role System Improvement (Constants-Based)

Refactor roles to class constants:

```php
class User
{
    public const ROLE_USER = 'user';
    public const ROLE_SUPER_ADMIN = 'super_admin';
}
```

Benefits:

* Avoid magic strings
* Safer comparisons
* Cleaner, maintainable code

---

## 📌 Project Vision

This system is being built toward a:

> **Scalable, API-first booking platform with queue-driven processing, secure authentication, and multi-client support (web, API).**

---

