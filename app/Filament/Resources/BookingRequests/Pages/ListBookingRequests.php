<?php

namespace App\Filament\Resources\BookingRequests\Pages;

use App\Enums\BookingRequestStatus;
use App\Filament\Resources\BookingRequests\BookingRequestResource;
use App\Models\BookingRequest;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListBookingRequests extends ListRecords
{
    protected static string $resource = BookingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        $tabs = [];

        // Tab الكل
        $tabs['all'] = Tab::make('All')
            ->icon('heroicon-o-rectangle-stack')
            ->badge(BookingRequest::count());

        // Tabs حسب Enum
        foreach (BookingRequestStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->label())
                ->icon($status->icon())
                ->modifyQueryUsing(fn ($query) =>
                $query->where('status', $status->value)
                )
                ->badge(
                    BookingRequest::where('status', $status->value)->count()
                )
                ->badgeColor($status->color());
        }

        return $tabs;
    }
}
