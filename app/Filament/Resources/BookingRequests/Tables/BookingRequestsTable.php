<?php

namespace App\Filament\Resources\BookingRequests\Tables;

use App\Enums\BookingRequestStatus;
use App\Models\BookingRequest;
use App\Models\Course;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
            ->columns([
                TextColumn::make('courseRun.course.title')
                    ->label('Course')
                    ->formatStateUsing(fn ($state, BookingRequest $record) => $record->courseRun?->course?->getTranslation('title', app()->getLocale()) ?? '-')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('courseRun.course', function (Builder $q) use ($search) {
                            $q->where('title->en', 'like', "%{$search}%")
                                ->orWhere('title->ar', 'like', "%{$search}%");
                        });
                    })
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

//                TextColumn::make('email')
//                    ->label('Email')
//                    ->searchable()
//                    ->copyable()
//                    ->placeholder('-')
//                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($state) {
                        $value = $state instanceof \BackedEnum ? $state->value : $state;

                        return match ($value) {
                            BookingRequestStatus::New->value => 'gray',
                            BookingRequestStatus::Contacted->value => 'warning',
                            BookingRequestStatus::Confirmed->value => 'success',
                            BookingRequestStatus::Rejected->value => 'danger',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        $value = $state instanceof \BackedEnum ? $state->value : $state;

                        return match ($value) {
                            BookingRequestStatus::New->value => 'New',
                            BookingRequestStatus::Contacted->value => 'Contacted',
                            BookingRequestStatus::Confirmed->value => 'Confirmed',
                            BookingRequestStatus::Rejected->value => 'Rejected',
                            default => (string) $value,
                        };
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::statusOptions())
                    ->placeholder('All'),

                // Filter by specific run
                SelectFilter::make('course_run_id')
                    ->label('Run')
                    ->relationship('courseRun', 'id')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        // $record هون هو CourseRun
                        return sprintf(
                            '%s — %s',
                            $record->course?->getTranslation('title', app()->getLocale()) ?? 'Course',
                            optional($record->starts_at)->format('Y-m-d H:i') ?? '-'
                        );
                    })
                    ->searchable()
                    ->preload(),

                // Filter by course (through courseRun.course)
                SelectFilter::make('course_id')
                    ->label('Course')
                    ->options(function (): array {
                        // لأن title JSON، منجيب كل كورس وبنطلع الترجمة حسب لغة النظام
                        return Course::query()
                            ->orderBy('id', 'desc')
                            ->get()
                            ->mapWithKeys(fn (Course $course) => [
                                $course->id => $course->getTranslation('title', app()->getLocale()),
                            ])
                            ->toArray();
                    })
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        $courseId = $data['value'] ?? null;

                        return $query->when($courseId, function (Builder $q) use ($courseId) {
                            return $q->whereHas('courseRun', fn (Builder $qr) => $qr->where('course_id', $courseId));
                        });
                    }),

                // Date presets: last hour + today + custom range
                Filter::make('submitted_at')
                    ->label('Submitted time')
                    ->form([
                        Select::make('preset')
                            ->label('Preset')
                            ->options([
                                'last_hour' => 'Last hour',
                                'today' => 'Today',
                                'custom' => 'Custom range',
                            ])
                            ->default('today'),

                        DateTimePicker::make('from')
                            ->label('From')
                            ->seconds(false)
                            ->visible(fn (callable $get) => $get('preset') === 'custom'),

                        DateTimePicker::make('to')
                            ->label('To')
                            ->seconds(false)
                            ->visible(fn (callable $get) => $get('preset') === 'custom'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $preset = $data['preset'] ?? 'today';

                        if ($preset === 'last_hour') {
                            return $query->where('created_at', '>=', now()->subHour());
                        }

                        if ($preset === 'today') {
                            return $query->whereDate('created_at', now()->toDateString());
                        }

                        // custom
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $from) => $q->where('created_at', '>=', $from))
                            ->when($data['to'] ?? null, fn (Builder $q, $to) => $q->where('created_at', '<=', $to));
                    }),
            ])
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

    private static function statusOptions(): array
    {
        return [
            BookingRequestStatus::New->value => 'New',
            BookingRequestStatus::Contacted->value => 'Contacted',
            BookingRequestStatus::Confirmed->value => 'Confirmed',
            BookingRequestStatus::Rejected->value => 'Rejected',
        ];
    }
}
