<?php

namespace App\Filament\Resources\CourseRuns\Schemas;

use App\Enums\CourseRunStatus;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseRunInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('course.title')
                        ->label('Course')
                        ->formatStateUsing(fn ($state, $record) => $record->course?->getTranslation('title', app()->getLocale()) ?? '-')
                        ->placeholder('-'),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->formatStateUsing(function ($state): string {
                            if ($state instanceof CourseRunStatus) {
                                return $state->label();
                            }

                            // إذا كانت string (مثلاً قبل الـ cast أو بسياقات معينة)
                            return CourseRunStatus::tryFrom((string) $state)?->label() ?? '-';
                        })
                        ->color(function ($state): string {
                            if ($state instanceof CourseRunStatus) {
                                return $state->color();
                            }

                            return CourseRunStatus::tryFrom((string) $state)?->color() ?? 'gray';
                        }),
                    TextEntry::make('starts_at')
                        ->label('Starts at')
                        ->dateTime()
                        ->placeholder('-'),

                    TextEntry::make('ends_at')
                        ->label('Ends at')
                        ->dateTime()
                        ->placeholder('-'),

//                    TextEntry::make('capacity')
//                        ->label('Capacity')
//                        ->formatStateUsing(fn ($state) => filled($state) ? number_format((int) $state) : 'Unlimited'),
//
//                    TextEntry::make('price')
//                        ->label('Price')
//                        ->money('USD')
//                        ->placeholder('-'),

                    IconEntry::make('is_active')
                        ->label('Active')
                        ->boolean(),
                ])->columnSpanFull(),

            Section::make('Meta')
                ->columns(2)
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Created at')
                        ->dateTime()
                        ->placeholder('-'),

                    TextEntry::make('updated_at')
                        ->label('Updated at')
                        ->dateTime()
                        ->placeholder('-'),
                ])->columnSpanFull(),
        ]);
    }
}
