<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,

            'title' => $this->getTranslation('title', $locale),
            'description' => $this->description
                ? $this->getTranslation('description', $locale)
                : null,

            // duration_hours اللي اتفقنا عليها
            'duration_hours' => $this->duration_hours ?? null,

            'is_active' => (bool) $this->is_active,

            // علاقات اختيارية (بتطلع بس إذا محمّلة with)
            'category' => $this->whenLoaded('category')->name,

            // media
            'cover_url' =>  $this->getFirstMediaUrl('cover') ?: null
            ,
            'gallery' => $this->getMedia('gallery')->map(fn ($m) => $m->getUrl())->values()?: [],

            'course_runs' => CourseRunResource::Collection($this->whenLoaded('courseRuns'))?:[]
            ];
    }
}
