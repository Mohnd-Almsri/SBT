<?php

namespace App\Observers;

use App\Enums\CourseRunStatus;
use App\Models\CourseRun;

class CourseRunObserver
{
    /**
     * Handle the CourseRun "created" event.
     */
    public function created(CourseRun $courseRun): void
    {
        //
    }

    /**
     * Handle the CourseRun "updated" event.
     */
    public function saving(CourseRun $courseRun): void
    {
        // بما إن status Enum (cast) فالمقارنة لازم تكون Enum مع Enum
        $courseRun->is_active = ($courseRun->status === CourseRunStatus::Open);
    }
    public function updated(CourseRun $courseRun): void
    {


    }

    /**
     * Handle the CourseRun "deleted" event.
     */
    public function deleted(CourseRun $courseRun): void
    {
        //
    }

    /**
     * Handle the CourseRun "restored" event.
     */
    public function restored(CourseRun $courseRun): void
    {
        //
    }

    /**
     * Handle the CourseRun "force deleted" event.
     */
    public function forceDeleted(CourseRun $courseRun): void
    {
        //
    }
}
