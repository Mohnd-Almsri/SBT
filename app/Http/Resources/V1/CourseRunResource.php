<?php

namespace App\Http\Resources\V1;

use BackedEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseRunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = $this->status instanceof BackedEnum ? $this->status->value : $this->status;

        return [
            'id' => $this->id,
            'course_id' => $this->course_id,

            'starts_at' => optional($this->starts_at)->toISOString(),
            'ends_at' => optional($this->ends_at)->toISOString(),

//            'capacity' => $this->capacity !== null ? (int) $this->capacity : null,
//            'price' => $this->price !== null ? (float) $this->price : null,

            'status' => $status,
            'is_active' => (bool) $this->is_active,

            // optional relation
            'course' => CourseResource::make($this->whenLoaded('course')),
        ];
    }
}
