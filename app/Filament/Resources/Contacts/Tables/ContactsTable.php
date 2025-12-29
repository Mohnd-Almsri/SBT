<?php

namespace App\Filament\Resources\Contacts\Tables;

use App\Models\Contact;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean(),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('First Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Last Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('subject')
                    ->limit(40)
                    ->placeholder('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read status'),
            ])
            ->actions([
               ViewAction::make(),
                Action::make('mark_read')
                    ->label('Mark read')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Contact $record) => ! $record->is_read)
                    ->action(function (Contact $record) {
                        $record->update([
                            'is_read' => true,
                            'read_at' => now(),
                        ]);
                    }),

                Action::make('mark_unread')
                    ->label('Mark unread')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->visible(fn (Contact $record) => $record->is_read)
                    ->action(function (Contact $record) {
                        $record->update([
                            'is_read' => false,
                            'read_at' => null,
                        ]);
                    }),

                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_read_bulk')
                        ->label('Mark read')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_read' => true, 'read_at' => now()]);
                            }
                        }),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
