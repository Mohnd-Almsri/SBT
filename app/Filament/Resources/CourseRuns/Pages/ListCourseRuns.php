<?php

namespace App\Filament\Resources\CourseRuns\Pages;

use App\Enums\CourseRunStatus;
use App\Filament\Resources\CourseRuns\CourseRunResource;
use App\Models\CourseRun;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCourseRuns extends ListRecords
{
    protected static string $resource = CourseRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')
                ->badge(CourseRun::count()),
        ];

        foreach (CourseRunStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->label())
                ->modifyQueryUsing(
                    fn ($query) => $query->where('status', $status->value)
                )
                ->badge(
                    CourseRun::where('status', $status->value)->count()
                )
                ->badgeColor($status->color());
        }

        return $tabs;
    }
}
