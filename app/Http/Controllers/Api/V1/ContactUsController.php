<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ContactUsRequest;
use App\Services\V1\ContactUs\ContactUsServices;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function __construct(private readonly ContactUsServices $service) {}

    public function store(ContactUsRequest $request)
    {
        // هلق مؤقتًا بدون FormRequest — بعد شوي منعمل StoreBookingRequestRequest
        $contact = $this->service->create($request->all());

        return ApiResponse::success(data:[
            'contact' => $contact,
        ], message: 'Contact report submitted', status: 201);
    }
}
