<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'description' => $this->description
                ? $this->getTranslation('description', app()->getLocale())
                : null,
            'sort_order' => $this->sort_order,
            'is_active' => (bool) $this->is_active,

            // إذا حاب تطلع رابط الغلاف:
            'cover_url' => $this->getFirstMediaUrl('cover') ?: null
        ];

    }
}
