<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'space' => new SpaceResource($this->whenLoaded('space')),
            'email' => $this->email,
            'seat_number' => $this->seat_number,
            'start_time' => $this->start_time?->toDateTimeString(),
            'end_time' => $this->end_time?->toDateTimeString(),
            'status' => $this->status,
            'paid_at' => $this->paid_at ? Carbon::parse($this->paid_at)->toDateTimeString() : null,
            'hold_expires_at' => $this->hold_expires_at ? Carbon::parse($this->hold_expires_at)->toDateTimeString() : null,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
