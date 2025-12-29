<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\CourseRuns\CourseRunResource;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class CourseRunsRelationManager extends RelationManager
{
    protected static string $relationship = 'courseRuns';

    protected static ?string $relatedResource = CourseRunResource::class;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
