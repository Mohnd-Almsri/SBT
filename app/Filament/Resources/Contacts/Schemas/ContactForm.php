<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone')
                    ->required()
                    ->maxLength(50),

                Textarea::make('message')
                    ->label('Message')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
