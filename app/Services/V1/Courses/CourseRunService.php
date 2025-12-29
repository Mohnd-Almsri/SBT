<?php

namespace App\Services\V1\Courses;

use App\Models\CourseRun;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseRunService
{
    public function paginateActive(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = CourseRun::query()
            ->with(['course.category'])
            ->where('is_active', true);

        // filter by course_id
        if (!empty($filters['course_id'])) {
            $query->where('course_id', (int) $filters['course_id']);
        }

        // filter by status (open/closed/cancelled/draft)
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // upcoming only
        if (!empty($filters['upcoming'])) {
            $query->where('starts_at', '>=', now());
        }

        return $query->orderBy('starts_at')->paginate($perPage);
    }

    public function findActiveOrFail(int $id): CourseRun
    {
        return CourseRun::query()
            ->bookable()
            ->with(['course.category'])
            ->where('is_active', true)
            ->findOrFail($id);
    }
}
