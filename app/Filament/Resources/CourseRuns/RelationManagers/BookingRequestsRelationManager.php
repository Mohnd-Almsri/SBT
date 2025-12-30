<?php

namespace App\Filament\Resources\CourseRuns\RelationManagers;

use App\Filament\Resources\BookingRequests\BookingRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class BookingRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookingRequests';

    protected static ?string $relatedResource = BookingRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
//                CreateAction::make(),
            ]);
    }
}
