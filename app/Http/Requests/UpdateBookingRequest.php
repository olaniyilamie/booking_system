<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorize using policy: only booking owner or super_admin can update
        $booking = $this->route('booking');
        if (! $booking) {
            return false;
        }

        $user = $this->user();
        if (! $user) {
            return false;
        }

        return $user->can('update', $booking);
    }

    public function rules(): array
    {
        return [
            'start_time' => 'required|date_format:Y-m-d H:i:s|after:now',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'status' => 'sometimes|in:pending,confirmed,cancelled',
            'seat_number' => 'sometimes|integer|min:1',
        ];
    }
}
