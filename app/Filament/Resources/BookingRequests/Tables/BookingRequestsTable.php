<?php

namespace App\Filament\Resources\BookingRequests\Tables;

use App\Enums\BookingRequestStatus;
use App\Models\BookingRequest;
use App\Models\Course;
use App\Models\CourseRun;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(self::columns())
            ->defaultSort('created_at', 'desc')
            ->filters(self::filters())
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function columns(): array
    {
        return [
            TextColumn::make('courseRun.course.title')
                ->label('Course')
                ->formatStateUsing(fn ($state, BookingRequest $record) => self::courseTitle($record) ?? '-')
                ->searchable(query: fn (Builder $query, string $search) => self::applyCourseTitleSearch($query, $search))
                ->wrap(),

            TextColumn::make('courseRun.starts_at')
                ->label('Run Starts')
                ->dateTime()
                ->sortable()
                ->toggleable(),

            TextColumn::make('first_name')
                ->label('First Name')
                ->searchable()
                ->sortable(),

            TextColumn::make('last_name')
                ->label('Last Name')
                ->searchable()
                ->sortable(),

            TextColumn::make('phone')
                ->label('Phone')
                ->searchable()
                ->copyable()
                ->placeholder('-')
                ->toggleable(),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->icon(fn (BookingRequest $record) => $record->status?->icon())
                ->color(fn (BookingRequest $record) => $record->status?->color() ?? 'gray')
                ->formatStateUsing(fn (BookingRequest $record) => $record->status?->label() ?? '-')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Submitted')
                ->dateTime()
                ->sortable(),
        ];
    }

    private static function filters(): array
    {
        return [
            SelectFilter::make('status')
                ->label('Status')
                ->options(self::statusOptions())
                ->placeholder('All'),

            SelectFilter::make('course_run_id')
                ->label('Run')
                ->options(self::runOptions())
                ->searchable()
                ->preload(),

            SelectFilter::make('course_id')
                ->label('Course')
                ->options(self::courseOptions())
                ->searchable()
                ->query(fn (Builder $query, array $data) => self::filterByCourse($query, $data['value'] ?? null)),

            Filter::make('submitted_range')
                ->label('Submitted')
                ->form([
                    Select::make('range')
                        ->label('Range')
                        ->options([
                            'all' => 'All',
                            'today' => 'Today',
                            'last_24h' => 'Last 24 hours',
                            'last_7d' => 'Last 7 days',
                            'last_30d' => 'Last 30 days',
                        ])
                        ->default('all')
                        ->native(false),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $range = $data['range'] ?? 'all';

                    return match ($range) {
                        'today' => $query->whereDate('created_at', now()->toDateString()),
                        'last_24h' => $query->where('created_at', '>=', now()->subDay()),
                        'last_7d' => $query->where('created_at', '>=', now()->subDays(7)),
                        'last_30d' => $query->where('created_at', '>=', now()->subDays(30)),
                        default => $query, // all
                    };
                }),
        ];
    }

    // -------------------------
    // Small helpers (keep table clean)
    // -------------------------

    private static function locale(): string
    {
        return app()->getLocale();
    }

    private static function courseTitle(BookingRequest $record): ?string
    {
        return $record->courseRun?->course?->getTranslation('title', self::locale());
    }

    private static function applyCourseTitleSearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('courseRun.course', function (Builder $q) use ($search) {
            $q->where('title->en', 'like', "%{$search}%")
                ->orWhere('title->ar', 'like', "%{$search}%");
        });
    }

    private static function statusOptions(): array
    {
        return collect(BookingRequestStatus::cases())
            ->mapWithKeys(fn (BookingRequestStatus $status) => [$status->value => $status->label()])
            ->toArray();
    }

    private static function runOptions(): array
    {
        return CourseRun::query()
            ->with('course:id,title')
            ->orderByDesc('starts_at')
            ->get()
            ->mapWithKeys(function (CourseRun $run) {
                $label = sprintf(
                    '%s — %s',
                    $run->course?->getTranslation('title', self::locale()) ?? 'Course',
                    optional($run->starts_at)->format('Y-m-d H:i') ?? '-'
                );

                return [$run->id => $label];
            })
            ->toArray();
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

    private static function filterByCourse(Builder $query, $courseId): Builder
    {
        $courseId = $courseId ? (int) $courseId : null;

        return $query->when($courseId, function (Builder $q) use ($courseId) {
            $q->whereHas('courseRun', fn (Builder $qr) => $qr->where('course_id', $courseId));
        });
    }

    private static function applySubmittedAtFilter(Builder $query, array $data): Builder
    {
        $preset = $data['preset'] ?? 'today';

        return match ($preset) {
            'last_24h' => $query->where('created_at', '>=', now()->subDay()),
            'last_7d'  => $query->where('created_at', '>=', now()->subDays(7)),
            'custom'   => $query
                ->when($data['from'] ?? null, fn (Builder $q, $from) => $q->where('created_at', '>=', $from))
                ->when($data['to'] ?? null, fn (Builder $q, $to) => $q->where('created_at', '<=', $to)),
            default    => $query->whereDate('created_at', now()->toDateString()),
        };
    }

    private static function applyRunStartsFilter(Builder $query, array $data): Builder
    {
        return $query->whereHas('courseRun', function (Builder $q) use ($data) {
            $q->when($data['from'] ?? null, fn (Builder $qq, $from) => $qq->where('starts_at', '>=', $from))
                ->when($data['to'] ?? null, fn (Builder $qq, $to) => $qq->where('starts_at', '<=', $to));
        });
    }
}
