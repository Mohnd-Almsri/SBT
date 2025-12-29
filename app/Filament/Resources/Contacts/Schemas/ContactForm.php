<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            textInput::make('first_name')->label('First Name')->required(),
            textInput::make('last_name')->label('Last Name')->required(),
            textInput::make('email')->label('Email')->required(),
            textInput::make('phone')->label('Phone')->required(),
            textInput::make('subject')->label('Subject')->required(),
            textInput::make('message')->label('Message')->required()->columnSpanFull(),
            ])->columns(2);
    }
}
