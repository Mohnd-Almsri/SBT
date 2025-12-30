<?php

namespace App\Models;

use App\Enums\BookingRequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRequest extends Model
{
    protected $fillable = [
        'course_run_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'status',
        'note',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'status' => BookingRequestStatus::class,

    ];

    public function courseRun(): BelongsTo
    {
        return $this->belongsTo(CourseRun::class);
    }
    public function scopeNew(Builder $query) :Builder {
        return $query->where('status',BookingRequestStatus::New);
    }

}
