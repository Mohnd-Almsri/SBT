<?php

namespace App\Models;

use App\Enums\BookingRequestStatus;
use App\Enums\CourseRunStatus;
use App\Observers\CourseRunObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseRun extends Model
{
    protected $fillable = [
        'course_id',
        'starts_at',
        'ends_at',
        'capacity',
        'price',
        'status',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'capacity' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'status' => CourseRunStatus::class,
    ];

    protected static function booted(): void
    {
        static::observe(CourseRunObserver::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRealActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('status',CourseRunStatus::Open)
            ->whereHas('course', fn($q)=> $q->ActiveWithCategory()
            );
    }

    public function scopeNotRealActive(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('is_active', false)
                ->where('status','!=',CourseRunStatus::Open)
                ->orWhereHas('course', function (Builder $course) {
                    $course->where('is_active', false)
                        ->orWhereHas('category', fn (Builder $cat) => $cat->where('is_active', false))
                        ->orWhereDoesntHave('category');
                })
                ->orWhereDoesntHave('course');
        });
    }


    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', CourseRunStatus::Open);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('status', CourseRunStatus::Open);
    }

    public function scopeBookable(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('status', CourseRunStatus::Open)
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->whereHas('course', fn($q)=> $q->ActiveWithCategory()
            );
    }
}
