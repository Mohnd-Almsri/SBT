<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All')
                ->icon('heroicon-o-check-circle')
                ->badge(Category::count()),

            'Active' => Tab::make('Active')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('is_active', 1))
                ->badge(Category::where('is_active', 1)->count())
                ->badgeColor('success'),
            'Inactive' => Tab::make('Inactive')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('is_active', 0))
                ->badge(Category::where('is_active', 0)->count())
                ->badgeColor('danger'),

        ];
    }

}
