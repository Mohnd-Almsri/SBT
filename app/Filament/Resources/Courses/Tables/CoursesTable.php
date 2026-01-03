<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Models\Category;
use App\Models\Course;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(self::columns())
            ->filters(self::filters())
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function columns(): array
    {
        return [
            TextColumn::make('title')
                ->label('Course')
                ->state(fn (Course $record) => $record->getTranslation('title', self::locale()) ?? '-')
                ->searchable(query: fn (Builder $query, string $search) => self::searchJson($query, 'title', $search))
                ->sortable()
                ->wrap(),

            TextColumn::make('category.name')
                ->label('Category')
                ->state(fn (Course $record) => $record->category?->getTranslation('name', self::locale()) ?? '-')
                ->searchable(query: fn (Builder $query, string $search) => self::searchCategoryJson($query, $search))
                ->sortable()
                ->wrap(),

            TextColumn::make('duration_hours')
                ->label('Duration')
                ->suffix(' h')
                ->numeric()
                ->sortable(),

            TextColumn::make('course_runs_count')
                ->label('Runs')
                ->numeric()
                ->sortable(),

            IconColumn::make('category.is_active')
                ->label('Category Active')
                ->boolean(),

            IconColumn::make('is_active')
                ->label('Active')
                ->boolean()
                ->sortable(),

            IconColumn::make('is_featured')
                ->label('Featured')
                ->boolean()
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Created')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('updated_at')
                ->label('Updated')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    private static function filters(): array
    {
        return [
            SelectFilter::make('is_active')
                ->label('Active')
                ->options([
                    1 => 'Active',
                    0 => 'Inactive',
                ])
                ->placeholder('All'),

            SelectFilter::make('is_featured')
                ->label('Featured')
                ->options([
                    1 => 'Featured',
                    0 => 'Not featured',
                ])
                ->placeholder('All'),

            // فلتر الكاتيجوري (مفيد جداً)
            SelectFilter::make('category_id')
                ->label('Category')
                ->options(self::categoryOptions())
                ->searchable()
                ->preload()
                ->placeholder('All'),

            // Real active: الكورس فعال + الكاتيجوري فعالة (مفيد لإدارة الظهور للزبون)
            SelectFilter::make('real_active')
                ->label('Real active')
                ->options([
                    'real_active' => 'Real active',
                    'not_real_active' => 'Not real active',
                ])
                ->placeholder('All')
                ->query(function (Builder $query, array $data): Builder {
                    return match ($data['value'] ?? null) {
                        'real_active' => $query->activeWithCategory(), // سكوب عندك حسب ما ذكرت سابقاً
                        'not_real_active' => $query->where(function (Builder $q) {
                            $q->where('is_active', false)
                                ->orWhereHas('category', fn (Builder $cat) => $cat->where('is_active', false))
                                ->orWhereDoesntHave('category');
                        }),
                        default => $query,
                    };
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

    private static function searchCategoryJson(Builder $query, string $search): Builder
    {
        return $query->whereHas('category', function (Builder $q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
                ->orWhere('name->ar', 'like', "%{$search}%");
        });
    }

    private static function categoryOptions(): array
    {
        return Category::query()
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (Category $cat) => [
                $cat->id => $cat->getTranslation('name', self::locale()),
            ])
            ->toArray();
    }
}
