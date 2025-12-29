<?php

namespace App\Filament\Resources\BookingRequests\Schemas;

use App\Enums\BookingRequestStatus;
use App\Models\CourseRun;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Booking')
                ->columns(2)
                ->schema([
                    Select::make('course_run_id')
                        ->label('Run')
                        ->relationship('courseRun', 'id')
                        ->getOptionLabelFromRecordUsing(function (CourseRun $record) {
                            $courseTitle = $record->course?->getTranslation('title', app()->getLocale()) ?? 'Course';
                            $starts = optional($record->starts_at)->format('Y-m-d H:i') ?? '-';

                            return "{$courseTitle} — {$starts}";
                        })
                        ->disabled() // الطلب جاي من public، ما بدنا نغيره غالباً
                        ->dehydrated(false) // لا تبعت قيمته بالسيف
                        ->searchable()
                        ->preload(),

                    Select::make('status')
                        ->label('Status')
                        ->options(self::statusOptions())
                        ->required(),
                ]),

            Section::make('Applicant (read only)')
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')
                        ->label('First Name')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('last_name')
                        ->label('Last Name')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('phone')
                        ->label('Phone')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('email')
                        ->label('Email')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('address')
                        ->label('Address')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),

            Section::make('Admin')
                ->columnSpanFull()
                ->schema([
                    Textarea::make('note')
                        ->label('Admin note')
                        ->rows(5)
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    private static function statusOptions(): array
    {
        return collect(BookingRequestStatus::cases())
            ->mapWithKeys(function (BookingRequestStatus $case) {
                return [$case->value => ucfirst($case->value)];
            })
            ->toArray();
    }
}
