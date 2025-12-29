<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Course extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;

    public const MEDIA_COLLECTION_COVER = 'cover';
    public const MEDIA_COLLECTION_GALLERY = 'gallery';

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'is_active',
        'duration_hours'
        ,'is_featured'
    ];

    public array $translatable = [
        'title',
        'description',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function courseRuns(): HasMany
    {
        return $this->hasMany(CourseRun::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection(self::MEDIA_COLLECTION_COVER)
            ->singleFile()
            ->useDisk('public');

        $this
            ->addMediaCollection(self::MEDIA_COLLECTION_GALLERY)
            ->useDisk('public');
    }


    public function scopeActiveWithCategory(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('is_active', true));
    }

    public function scopeFeaturedWithActiveCategory(Builder $query): Builder
    {
        return $query->activeWithCategory()->where('is_featured', true);
    }

}
