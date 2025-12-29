<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Models\Course;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Course')
                    ->getStateUsing(fn (Course $record) => $record->getTranslation('title', app()->getLocale()) ?? '-')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $q) use ($search) {
                            $q->where('title->en', 'like', "%{$search}%")
                                ->orWhere('title->ar', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->getStateUsing(fn (Course $record) => $record->category?->getTranslation('name', app()->getLocale()) ?? '-')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('category', function (Builder $q) use ($search) {
                            $q->where('name->en', 'like', "%{$search}%")
                                ->orWhere('name->ar', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                TextColumn::make('duration_hours')
                    ->label('Duration')
                    ->suffix(" h")
                    ->numeric()
                    ->sortable(),

                TextColumn::make('course_runs_count')
                    ->label('Runs')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('category.is_active')
                    ->label('Category Active')
                    ->boolean()
                    ,

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
            ])
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
}
