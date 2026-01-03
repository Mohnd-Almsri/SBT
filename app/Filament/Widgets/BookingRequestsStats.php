<?php

namespace App\Filament\Widgets;

use App\Models\BookingRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingRequestsStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Booking Requests Today', BookingRequest::query()
                ->whereDate('created_at', today())
                ->count()
            )
                ->icon('heroicon-o-calendar-days'),

            Stat::make('Booking Requests (Last 24h)', BookingRequest::query()
                ->where('created_at', '>=', now()->subDay())
                ->count()
            )
                ->icon('heroicon-o-clock'),

            Stat::make('Booking Requests (Last 7d)', BookingRequest::query()
                ->where('created_at', '>=', now()->subDays(7))
                ->count()
            )
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}
