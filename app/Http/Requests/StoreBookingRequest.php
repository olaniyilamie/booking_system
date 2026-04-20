<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // allow both authenticated and guest users to create bookings
        return true;
    }

    public function rules(): array
    {
        return [
            'space_id' => 'required|exists:spaces,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'email' => 'sometimes|email',
            'seat_number' => 'sometimes|integer|min:1',
        ];
    }
}
