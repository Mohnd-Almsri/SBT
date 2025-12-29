<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Services\V1\Courses\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $service) {}

    public function index(Request $request)
    {
        $categories = $this->service->listActive();

        return ApiResponse::success([
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
