<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    protected static ?string $relatedResource = CourseResource::class;

    // ✅ هذا أهم سطر إذا بدك الأزرار تظهر على صفحة View
    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->placeholder('All'),
            ]);
    }
}
