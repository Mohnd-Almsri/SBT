<?php

namespace App\Filament\Resources\Contacts\Tables;

use App\Models\Contact;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ContactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(self::columns())
            ->defaultSort('created_at', 'desc')
            ->filters(self::filters())
            ->actions(self::actions())
            ->bulkActions(self::bulkActions());
    }

    private static function columns(): array
    {
        return [
            Tables\Columns\IconColumn::make('is_read')
                ->label('Read')
                ->boolean()
                ->sortable(),

            Tables\Columns\TextColumn::make('first_name')
                ->label('First Name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('last_name')
                ->label('Last Name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('phone')
                ->label('Phone')
                ->searchable()
                ->copyable()
                ->placeholder('-')
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('message')
                ->label('Message')
                ->limit(50)
                ->wrap()
                ->placeholder('-'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Received')
                ->dateTime()
                ->sortable(),
        ];
    }

    private static function filters(): array
    {
        return [
            Tables\Filters\TernaryFilter::make('is_read')
                ->label('Read status'),

            // مفيد جداً: آخر 24 ساعة / آخر 7 أيام / الكل
            Tables\Filters\Filter::make('received_range')
                ->label('Received')
                ->form([
                    Select::make('range')
                        ->label('Range')
                        ->options([
                            'all' => 'All',
                            'last_24h' => 'Last 24 hours',
                            'last_7d' => 'Last 7 days',
                            'today' => 'Today',
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
                        default => $query,
                    };
                }),
        ];
    }

    private static function actions(): array
    {
        return [
           ViewAction::make(),

            Action::make('mark_read')
                ->label('Mark read')
                ->icon('heroicon-o-check')
                ->visible(fn (Contact $record) => ! $record->is_read)
                ->action(fn (Contact $record) => self::markAsRead($record)),

            Action::make('mark_unread')
                ->label('Mark unread')
                ->icon('heroicon-o-arrow-uturn-left')
                ->visible(fn (Contact $record) => $record->is_read)
                ->action(fn (Contact $record) => self::markAsUnread($record)),

            DeleteAction::make(),
        ];
    }

    private static function bulkActions(): array
    {
        return [
            BulkActionGroup::make([
                BulkAction::make('mark_read_bulk')
                    ->label('Mark read')
                    ->icon('heroicon-o-check')
                    ->action(fn (Collection $records) => self::markManyAsRead($records)),

                BulkAction::make('mark_unread_bulk')
                    ->label('Mark unread')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->action(fn (Collection $records) => self::markManyAsUnread($records)),

                DeleteBulkAction::make(),
            ]),
        ];
    }

    // -------------------------
    // Small helpers (no duplication)
    // -------------------------

    private static function markAsRead(Contact $record): void
    {
        $record->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    private static function markAsUnread(Contact $record): void
    {
        $record->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    private static function markManyAsRead(Collection $records): void
    {
        Contact::query()
            ->whereIn('id', $records->modelKeys())
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    private static function markManyAsUnread(Collection $records): void
    {
        Contact::query()
            ->whereIn('id', $records->modelKeys())
            ->update([
                'is_read' => false,
                'read_at' => null,
            ]);
    }
}
