<?php

namespace App\Console\Commands;

use App\Enums\CourseRunStatus;
use App\Models\CourseRun;
use Illuminate\Console\Command;

class CloseExpiredCourseRuns extends Command
{
    protected $signature = 'course-runs:close-expired';
    protected $description = 'Close course runs that have ended';

    public function handle(): int
    {
        $now = now();

        $affected = CourseRun::query()
            ->where('status', CourseRunStatus::Open->value)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->update([
                'status' => CourseRunStatus::Closed->value,
                'is_active' => false,
                'updated_at' => $now,
            ]);

        $this->info("Closed runs: {$affected}");

        return self::SUCCESS;
    }
}
