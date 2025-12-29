<?php

namespace App\Filament\Resources\BookingRequests\Schemas;

use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Booking Info')
                ->columns(2)
                ->schema([
                    TextEntry::make('courseRun.course.title')
                        ->label('Course')
                        ->formatStateUsing(fn ($state, $record) =>
                            $record->courseRun?->course?->getTranslation('title', app()->getLocale()) ?? '-'
                        )
                        ->placeholder('-'),

                    TextEntry::make('courseRun.starts_at')
                        ->label('Run starts at')
                        ->dateTime()
                        ->placeholder('-'),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(function ($state) {
                            $value = $state instanceof BackedEnum ? $state->value : $state;

                            return match ($value) {
                                'new' => 'gray',
                                'contacted' => 'warning',
                                'confirmed' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            };
                        })
                        ->formatStateUsing(function ($state) {
                            $value = $state instanceof BackedEnum ? $state->value : $state;

                            return match ($value) {
                                'new' => 'New',
                                'contacted' => 'Contacted',
                                'confirmed' => 'Confirmed',
                                'rejected' => 'Rejected',
                                default => (string) $value,
                            };
                        }),

                    IconEntry::make('is_active')
                        ->label('Active')
                        ->boolean()
                        ->visible(fn ($record) => isset($record->is_active)),
                ]),

            Section::make('Applicant')
                ->columns(2)
                ->schema([
                    TextEntry::make('first_name')
                        ->label('First name')
                        ->placeholder('-'),

                    TextEntry::make('last_name')
                        ->label('Last name')
                        ->placeholder('-'),

                    TextEntry::make('phone')
                        ->label('Phone')
                        ->placeholder('-')
                        ->copyable(),

//                    TextEntry::make('email')
//                        ->label('Email')
//                        ->placeholder('-')
//                        ->copyable(),
//
//                    TextEntry::make('address')
//                        ->label('Address')
//                        ->placeholder('-')
//                        ->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('note')
                        ->label('Admin note')
                        ->placeholder('-')
                        ->prose(),
                ]),

            Section::make('Meta')
                ->collapsed()
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('meta')
                        ->label('Meta data')
                        ->formatStateUsing(fn ($state) =>
                        filled($state)
                            ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                            : '-'
                        )
                        ->prose(),

                    TextEntry::make('created_at')
                        ->label('Submitted at')
                        ->dateTime()
                        ->placeholder('-'),

                    TextEntry::make('updated_at')
                        ->label('Last update')
                        ->dateTime()
                        ->placeholder('-'),
                ]),
        ]);
    }
}
