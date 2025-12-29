<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\V1\Booking\BookingRequestService;
use Illuminate\Http\Request;

class BookingRequestController extends Controller
{
    public function __construct(private readonly BookingRequestService $service) {}

    public function store(Request $request)
    {
        // هلق مؤقتًا بدون FormRequest — بعد شوي منعمل StoreBookingRequestRequest
        $booking = $this->service->create($request->all());

        return ApiResponse::success(data:[
            'id' => $booking->id,
            'status' => $booking->status, // إذا enum رح يطلع object، لاحقًا مننسّقه بالـ Resource
        ], message: 'Booking request submitted', status: 201);
    }
}
