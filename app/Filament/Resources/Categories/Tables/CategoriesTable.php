<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(self::columns())
            ->filters(self::filters())
            ->defaultSort('sort_order')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    private static function columns(): array
    {
        return [
            SpatieMediaLibraryImageColumn::make('cover')
                ->label('Image')
                ->collection(Category::MEDIA_COLLECTION_COVER)
                ->disk('public')
                ->circular()
                ->size(60),

            TextColumn::make('name')
                ->label('Name')
                ->state(fn (Category $record) => $record->getTranslation('name', self::locale()) ?? '-')
                ->searchable(query: fn (Builder $query, string $search) => self::searchJson($query, 'name', $search))
                ->sortable(),

            TextColumn::make('description')
                ->label('Description')
                ->limit(75)
                ->state(fn (Category $record) => $record->getTranslation('description', self::locale()) ?? '-')
                ->searchable(query: fn (Builder $query, string $search) => self::searchJson($query, 'description', $search))
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
        ];
    }

    private static function filters(): array
    {
        return [
            // Active: All / Active / Inactive
            SelectFilter::make('is_active')
                ->label('Active')
                ->options([
                    '1' => 'Active',
                    '0' => 'Inactive',
                ])
                ->placeholder('All'),

            // Has courses: yes/no (مفيد جداً)
            Filter::make('has_courses')
                ->label('Has courses')
                ->form([
                    Select::make('value')
                        ->label('Has courses')
                        ->options([
                            '1' => 'Yes',
                            '0' => 'No',
                        ])
                        ->default('1')
                        ->native(false),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $value = $data['value'] ?? null;

                    return $query->when($value !== null, function (Builder $q) use ($value) {
                        return $value === '1'
                            ? $q->has('courses')
                            : $q->doesntHave('courses');
                    });
                }),
        ];
    }

    // -------------------------
    // Helpers
    // -------------------------

    private static function locale(): string
    {
        return app()->getLocale();
    }

    private static function searchJson(Builder $query, string $field, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($field, $search) {
            $q->where("{$field}->en", 'like', "%{$search}%")
                ->orWhere("{$field}->ar", 'like', "%{$search}%");
        });
    }
}
