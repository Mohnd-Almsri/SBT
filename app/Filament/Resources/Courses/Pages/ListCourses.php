<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Models\Course;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All')
                ->icon('heroicon-o-check-circle')
                ->badge(Course::count()),

            'Active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('is_active', 1))
                ->badge(Course::where('is_active', 1)->count())
                ->badgeColor('success'),
            'Inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('is_active', 0))
                ->badge(Course::where('is_active', 0)->count())
                ->badgeColor('danger'),

        ];
    }
}
