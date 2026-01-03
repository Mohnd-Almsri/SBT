<?php

namespace App\Filament\Widgets;

use App\Models\CourseRun;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingCourseRuns extends TableWidget
{
    protected static ?string $heading = 'Upcoming Course Runs (Next 14 days)';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                TextColumn::make('course.title')
                    ->label('Course')
                    ->state(fn (CourseRun $record) =>
                        $record->course?->getTranslation('title', app()->getLocale()) ?? '-'
                    )
                    ->wrap(),

                TextColumn::make('starts_at')
                    ->label('Starts at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('booking_requests_count')
                    ->label('Bookings')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('starts_at', 'asc');
    }

    private function getQuery(): Builder
    {
        return CourseRun::query()
            ->with(['course:id,title'])
            ->withCount('bookingRequests')
            ->whereBetween('starts_at', [now(), now()->addDays(14)]);
    }
}
