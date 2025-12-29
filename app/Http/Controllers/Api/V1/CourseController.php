<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CourseRunStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CourseResource;
use App\Models\Course;
use App\Services\V1\Courses\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private readonly CourseService $service) {}

    public function index(Request $request)
    {
        $perPage = max(1, (int) ($request->integer('per_page') ?: 12));

        $paginator = $this->service->paginateActive([
            'category_id' => $request->input('category_id'),
            'q' => $request->input('q'),
        ], $perPage);

        // لا 404 هون — خلّيها ترجع فاضي طبيعي
        return ApiResponse::paginated($paginator, [
            'courses' => CourseResource::collection($paginator->getCollection()),
        ]);
    }

    public function show(int $course)
    {
        $course = $this->service->findActiveOrFail($course);

        $course->load([
            'category',
            'courseRuns' => fn ($q) => $q->active()->where("status",CourseRunStatus::Open)->orderByDesc('starts_at'),
        ]);

        return ApiResponse::success([
            'course' => new CourseResource($course),
        ]);
    }

    public function showFeaturedCourses()
    {
        $courses = Course::query()
            ->featuredWithActiveCategory()
            ->with([
                'category',
                'courseRuns' => fn ($q) => $q->public()->latest('starts_at'),
            ])
            ->latest('id')
            ->limit(12)
            ->get();

        return ApiResponse::success([
            'courses' => CourseResource::collection($courses),
        ]);
    }

}
