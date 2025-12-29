<?php

namespace App\Services\V1\Courses;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function listActive(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->latest('id')
            ->get();
    }
}
