<?php

namespace App\Filament\Resources\Courses;

use App\Enums\CourseRunStatus;
use App\Filament\Resources\Courses\Pages\CreateCourse;
use App\Filament\Resources\Courses\Pages\EditCourse;
use App\Filament\Resources\Courses\Pages\ListCourses;
use App\Filament\Resources\Courses\Pages\ViewCourse;
use App\Filament\Resources\Courses\RelationManagers\CourseRunsRelationManager;
use App\Filament\Resources\Courses\Schemas\CourseForm;
use App\Filament\Resources\Courses\Schemas\CourseInfolist;
use App\Filament\Resources\Courses\Tables\CoursesTable;
use App\Models\Course;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /**
     * حتى لو title JSON، Filament ما رح يستخدمه مباشرة
     * لأننا مخصصين العرض بالـ table / infolist
     */
    protected static ?string $recordTitleAttribute = 'title';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'category:id,name,is_active',
            ])
            ->withCount('courseRuns')
            ->withMin(
                ['courseRuns as next_run_starts_at' => function (Builder $query) {
                    $query
                        ->where('is_active', true)
                        ->whereIn('status', [
                            CourseRunStatus::Open->value,
                            CourseRunStatus::Draft->value,
                        ])
                        ->where('starts_at', '>=', now());
                }],
                'starts_at'
            );
    }

    public static function form(Schema $schema): Schema
    {
        return CourseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoursesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CourseRunsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'view'   => ViewCourse::route('/{record}'),
            'edit'   => EditCourse::route('/{record}/edit'),
        ];
    }
}
