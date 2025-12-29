<?php

namespace App\Http\Resources\V1;

use App\Enums\BookingRequestStatus;
use BackedEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $raw = $this->status instanceof BackedEnum ? $this->status->value : $this->status;

        return [
            'id' => $this->id,
            'course_run_id' => $this->course_run_id,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim($this->first_name . ' ' . $this->last_name),

            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,

            'status' => $raw,

            // label اختياري للعرض
            'status_label' => match ($raw) {
                BookingRequestStatus::New->value => 'New',
                BookingRequestStatus::Contacted->value => 'Contacted',
                BookingRequestStatus::Confirmed->value => 'Confirmed',
                BookingRequestStatus::Rejected->value => 'Rejected',
                default => (string) $raw,
            },

            'note' => $this->note,
            'meta' => $this->meta,

            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
