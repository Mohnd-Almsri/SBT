<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;

    public const MEDIA_COLLECTION_COVER = 'cover';

    protected $fillable = [
        'name',
        'description',
        'slug',
        'sort_order',
        'is_active',
    ];

    public array $translatable = [
        'name',
        'description',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /** علاقة الكورسات */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /** Collection للصورة (صورة واحدة فقط) */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::MEDIA_COLLECTION_COVER)
            ->singleFile()
            ->useDisk('public');
    }

    /** Scope مفيد للـ API */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
