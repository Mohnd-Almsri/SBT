<?php

namespace App\Filament\Resources\CourseRuns;

use App\Filament\Resources\CourseRuns\Pages\CreateCourseRun;
use App\Filament\Resources\CourseRuns\Pages\EditCourseRun;
use App\Filament\Resources\CourseRuns\Pages\ListCourseRuns;
use App\Filament\Resources\CourseRuns\Pages\ViewCourseRun;
use App\Filament\Resources\CourseRuns\RelationManagers\BookingRequestsRelationManager;
use App\Filament\Resources\CourseRuns\Schemas\CourseRunForm;
use App\Filament\Resources\CourseRuns\Schemas\CourseRunInfolist;
use App\Filament\Resources\CourseRuns\Tables\CourseRunsTable;
use App\Models\CourseRun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseRunResource extends Resource
{
    protected static ?string $model = CourseRun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    // intentionally removed recordTitleAttribute
    // title will be handled in table / infolist
    protected static null|string|\UnitEnum $navigationGroup = 'Courses';
    protected static ?int $navigationSort = 30;
//    protected static ?string $navigationLabel = 'Categories';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['course:id,title,is_active,category_id', 'course.category:id,is_active'])
            ->withCount('bookingRequests');
    }


    public static function form(Schema $schema): Schema
    {
        return CourseRunForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseRunInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseRunsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        BookingRequestsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseRuns::route('/'),
            'create' => CreateCourseRun::route('/create'),
            'view' => ViewCourseRun::route('/{record}'),
            'edit' => EditCourseRun::route('/{record}/edit'),
        ];
    }
}
