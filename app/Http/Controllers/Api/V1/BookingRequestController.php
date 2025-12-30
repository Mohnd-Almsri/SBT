<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreBookingRequestRequest;
use App\Services\V1\Booking\BookingRequestService;

class BookingRequestController extends Controller
{
    public function __construct(private readonly BookingRequestService $service) {}

    public function store(StoreBookingRequestRequest $request)
    {
        $booking = $this->service->create($request->validated());

        return ApiResponse::success(
            data: [
                'id' => $booking->id,
                'status' => $booking->status, // لاحقًا منعمل Resource ينظّم enum
            ],
            message: 'Booking request submitted',
            status: 201
        );
    }
}
