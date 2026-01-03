<?php

namespace App\Filament\Resources\CourseRuns\Tables;

use App\Enums\CourseRunStatus;
use App\Models\Course;
use App\Models\CourseRun;
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
            ->columns(self::columns())
            ->filters(self::filters())
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

    private static function columns(): array
    {
        return [
            TextColumn::make('course.title')
                ->label('Course')
                ->state(fn (CourseRun $record) => $record->course?->getTranslation('title', self::locale()) ?? '-')
                ->searchable(query: fn (Builder $query, string $search) => self::applyCourseTitleSearch($query, $search))
                ->wrap(),

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
                ->formatStateUsing(fn ($state) => self::statusLabel($state))
                ->color(fn ($state) => self::statusColor($state))
                ->sortable(),

            IconColumn::make('course.category.is_active')
                ->label('Category Active')
                ->boolean(),

            IconColumn::make('course.is_active')
                ->label('Course Active')
                ->boolean(),

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
        ];
    }

    private static function filters(): array
    {
        return [
            SelectFilter::make('status')
                ->label('Status')
                ->options(CourseRunStatus::options())
                ->placeholder('All'),

            SelectFilter::make('is_active')
                ->label('Active')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $value = $data['value'] ?? null;

                    return $query->when($value, function (Builder $q) use ($value) {
                        return match ($value) {
                            'active' => $q->realActive(),
                            'inactive' => $q->notRealActive(),
                            default => $q,
                        };
                    });
                })
                ->placeholder('All'),

            // مفيد جداً: فلترة حسب الكورس مباشرة
            SelectFilter::make('course_id')
                ->label('Course')
                ->options(self::courseOptions())
                ->searchable()
                ->preload(),


        ];
    }

    // -------------------------
    // Helpers
    // -------------------------

    private static function locale(): string
    {
        return app()->getLocale();
    }

    private static function applyCourseTitleSearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('course', function (Builder $q) use ($search) {
            $q->where('title->en', 'like', "%{$search}%")
                ->orWhere('title->ar', 'like', "%{$search}%");
        });
    }

    private static function statusEnum($state): ?CourseRunStatus
    {
        if ($state instanceof CourseRunStatus) {
            return $state;
        }

        return CourseRunStatus::tryFrom((string) $state);
    }

    private static function statusLabel($state): string
    {
        return self::statusEnum($state)?->label() ?? '-';
    }

    private static function statusColor($state): string
    {
        return self::statusEnum($state)?->color() ?? 'gray';
    }

    private static function courseOptions(): array
    {
        return Course::query()
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (Course $course) => [
                $course->id => $course->getTranslation('title', self::locale()),
            ])
            ->toArray();
    }
}
