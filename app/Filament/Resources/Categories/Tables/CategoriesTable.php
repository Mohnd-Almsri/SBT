<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Image')
                    ->collection(Category::MEDIA_COLLECTION_COVER)
                    ->disk('public')
                    ->circular()
                    ->size(60),

                TextColumn::make('name')
                    ->label('Name')
                    ->getStateUsing(fn (Category $record) =>
                        $record->getTranslation('name', app()->getLocale()) ?? '-'
                    )
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search) {
                            $q->where('name->en', 'like', "%{$search}%")
                                ->orWhere('name->ar', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(75)
                    ->getStateUsing(fn (Category $record) =>
                        $record->getTranslation('description', app()->getLocale()) ?? '-'
                    )
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search) {
                            $q->where('description->en', 'like', "%{$search}%")
                                ->orWhere('description->ar', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('courses_count')
                    ->label('Courses')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
