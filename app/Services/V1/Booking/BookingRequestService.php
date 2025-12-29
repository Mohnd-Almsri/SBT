<?php

namespace App\Services\V1\Booking;

use App\Enums\BookingRequestStatus;
use App\Models\BookingRequest;
use App\Models\CourseRun;

class BookingRequestService
{
    public function create(array $data): BookingRequest
    {
        $run = CourseRun::query()
            ->whereKey((int) $data['course_run_id'])
            ->bookable()
            ->firstOrFail();

        // لاحقًا: check capacity (عدد الطلبات المقبولة/الحجوزات)
        // $this->ensureCapacity($run);

        return BookingRequest::query()->create([
            'course_run_id' => $run->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'status' => BookingRequestStatus::New,
            'note' => $data['note'] ?? null,
            'meta' => $data['meta'] ?? null,
        ]);
    }
}
