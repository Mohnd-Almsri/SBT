<?php

namespace App\Filament\Resources\CourseRuns\Pages;

use App\Filament\Resources\CourseRuns\CourseRunResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourseRun extends ViewRecord
{
    protected static string $resource = CourseRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
