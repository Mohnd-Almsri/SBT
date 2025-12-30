<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Details')
                    ->schema([

                        TextEntry::make('first_name')->label('First Name'),
                        TextEntry::make('last_name')->label('Last Name'),
                        TextEntry::make('phone')->label('Phone')->copyable(),

                        TextEntry::make('created_at')
                            ->label('Received')
                            ->dateTime(),

                        TextEntry::make('read_at')
                            ->label('Read at')
                            ->dateTime()
                            ->placeholder('-'),

                        IconEntry::make('is_read')
                            ->label('Read')
                            ->boolean(),


                        TextEntry::make('message')
                            ->label('Message')
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)->columnSpanFull(),
            ]);
    }
}
