<?php

namespace App\Services\V1\Courses;

use App\Enums\CourseRunStatus;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseService
{
    public function paginateActive(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Course::activeWithCategory()->with([
            'category',
            'courseRuns' => fn ($q) => $q->bookable()->orderByDesc('starts_at'),
        ]);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('title->en', 'like', "%{$search}%")
                    ->orWhere('title->ar', 'like', "%{$search}%");
            });
        }

        return $query->latest('id')->paginate($perPage);
    }

    public function findActiveOrFail(int $id): Course
    {
        return Course::query()
            ->activeWithCategory()
            ->with([
                'category',
                'courseRuns' => fn ($q) => $q->public()->orderByDesc('starts_at'),
            ])
            ->findOrFail($id);
    }

}
