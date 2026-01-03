<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnreadContactsStat extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Unread Contacts', Contact::query()
                ->where('is_read', false)
                ->count()
            )
                ->icon('heroicon-o-envelope')
                ->color('danger'),
        ];
    }
}
