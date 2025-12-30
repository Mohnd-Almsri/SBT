<?php

namespace App\Services\V1\ContactUs;

use App\Enums\BookingRequestStatus;
use App\Models\BookingRequest;
use App\Models\Contact;
use App\Models\CourseRun;

class ContactUsServices
{
    public function create(array $data): Contact
    {
       return Contact::create($data);
    }
}
