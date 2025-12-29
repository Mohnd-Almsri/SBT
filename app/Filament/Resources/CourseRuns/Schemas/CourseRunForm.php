<?php

namespace App\Filament\Resources\CourseRuns\Schemas;

use App\Enums\CourseRunStatus;
use App\Models\Course;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CourseRunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Course run')
                ->columns(2)
                ->schema([
                    Select::make('course_id')
                        ->label('Course')
                        ->relationship(
                            name: 'course',
                            titleAttribute: 'title', // منطقي حتى لو JSON، والليبل الحقيقي جاي من callback
                            modifyQueryUsing: fn (Builder $query) => $query
                                ->select(['id', 'title'])
//                                ->where('is_active', true) // اختياري: بس الكورسات الفعّالة
                        )
                        ->searchable()
                        ->preload()
                        ->getOptionLabelFromRecordUsing(function (Model $record): string {
                            /** @var Course $record */
                            return $record->getTranslation('title', app()->getLocale()) ?: "#{$record->id}";
                        })
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options(CourseRunStatus::options())
                        ->default(CourseRunStatus::Open->value)
                        ->required(),

                    DateTimePicker::make('starts_at')
                        ->label('Starts at')
                        ->timezone('Asia/Damascus')
                        ->seconds(false)
                        ->required(),

                    DateTimePicker::make('ends_at')
                        ->label('Ends at')
                        ->timezone('Asia/Damascus')
                        ->seconds(false)
                        ->afterOrEqual('starts_at')
                        ->nullable(),

//                    TextInput::make('capacity')
//                        ->label('Capacity')
//                        ->numeric()
//                        ->minValue(1)
//                        ->nullable()
//                        ->helperText('Leave empty for unlimited'),

//                    TextInput::make('price')
//                        ->label('Price')
//                        ->numeric()
//                        ->minValue(0)
//                        ->step('0.01')
//                        ->nullable()
//                        ->prefix('$'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columnSpanFull(),
        ]);
    }
}
