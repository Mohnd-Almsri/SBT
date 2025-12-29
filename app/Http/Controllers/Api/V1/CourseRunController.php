<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CourseRunResource;
use App\Models\CourseRun;
use App\Services\V1\Courses\CourseRunService;
use Illuminate\Http\Request;

class CourseRunController extends Controller
{
    public function __construct(private readonly CourseRunService $service) {}

    public function index(Request $request)
    {
        $perPage = (int) ($request->integer('per_page') ?: 12);

        $paginator = $this->service->paginateActive([
            'course_id' => $request->input('course_id'),
            'status' => $request->input('status'),
            'upcoming' => $request->boolean('upcoming'),
        ], $perPage);

        return ApiResponse::paginated($paginator, [
            'course_runs' => CourseRunResource::collection($paginator->getCollection()),


        ]);
    }

    public function show(CourseRun $courseRun)
    {
        return ApiResponse::success([
            'course_run' => new CourseRunResource($courseRun),
        ]);
    }
}
