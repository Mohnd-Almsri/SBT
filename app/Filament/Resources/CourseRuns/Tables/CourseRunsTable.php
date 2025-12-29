<?php

namespace App\Filament\Resources\CourseRuns\Tables;

use App\Enums\CourseRunStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseRunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.title')
                    ->label('Course')
                    ->getStateUsing(fn ($record) =>
                        $record->course?->getTranslation('title', app()->getLocale()) ?? '-'
                    )
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('course', function (Builder $q) use ($search) {
                            $q->where('title->en', 'like', "%{$search}%")
                                ->orWhere('title->ar', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Ends')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('booking_requests_count')
                    ->label('Total Bookings')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($state): string {
                        $status = $state instanceof CourseRunStatus
                            ? $state
                            : CourseRunStatus::tryFrom((string) $state);

                        return $status?->label() ?? '-';
                    })
                    ->color(function ($state): string {
                        $status = $state instanceof CourseRunStatus
                            ? $state
                            : CourseRunStatus::tryFrom((string) $state);

                        return $status?->color() ?? 'gray';
                    })
                    ->sortable(),

                IconColumn::make('course.category.is_active')
                    ->label('Category Active')
                    ->boolean()
                    ,

                IconColumn::make('course.is_active')
                    ->label('Course Active')
                    ->boolean()
                    ,

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(CourseRunStatus::options()),

                SelectFilter::make('is_active')
                    ->label('Active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),

                Filter::make('starts_at_range')
                    ->label('Start date range')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('to')->label('To'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['from'] ?? null),
                                fn (Builder $q) => $q->whereDate('starts_at', '>=', $data['from'])
                            )
                            ->when(
                                filled($data['to'] ?? null),
                                fn (Builder $q) => $q->whereDate('starts_at', '<=', $data['to'])
                            );
                    }),
            ])
            ->defaultSort('starts_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
