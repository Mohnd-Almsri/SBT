<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')
                ->columnSpanFull()
                ->schema([
                    Section::make('Translations')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name_ar')
                                ->label('Name (AR)')
                                ->getStateUsing(fn (Category $record) =>
                                    $record->getTranslation('name', 'ar') ?? '-'
                                ),

                            TextEntry::make('name_en')
                                ->label('Name (EN)')
                                ->getStateUsing(fn (Category $record) =>
                                    $record->getTranslation('name', 'en') ?? '-'
                                ),

                            TextEntry::make('description_ar')
                                ->label('Description (AR)')
                                ->getStateUsing(fn (Category $record) =>
                                    $record->getTranslation('description', 'ar') ?? '-'
                                )
                                ->prose(),

                            TextEntry::make('description_en')
                                ->label('Description (EN)')
                                ->getStateUsing(fn (Category $record) =>
                                    $record->getTranslation('description', 'en') ?? '-'
                                )
                                ->prose(),
                        ]),

                    Section::make('Details')
                        ->columns(2)
                        ->schema([

                            TextEntry::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->placeholder('-'),

                            IconEntry::make('is_active')
                                ->label('Active')
                                ->boolean(),

                            TextEntry::make('id')
                                ->label('ID'),
                        ]),

                    Section::make('Image')
                        ->schema([
                            ImageEntry::make('cover')
                                ->label('Category Image')
                                ->getStateUsing(fn (Category $record) =>
                                $record->getFirstMediaUrl(Category::MEDIA_COLLECTION_COVER) ?: null
                                )
                                ->url(fn (?string $state) => $state)
                                ->openUrlInNewTab()
                                ->height(180)
                                ->visible(fn (?string $state) => filled($state)),
                        ]),

                    Section::make('Courses')
                        ->columns(1)
                        ->schema([
                            TextEntry::make('courses_count')
                                ->label('Total Courses')
                                ->numeric(),
                        ]),

                    Section::make('Timestamps')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime(),

                            TextEntry::make('updated_at')
                                ->label('Updated At')
                                ->dateTime(),
                        ]),
                ]),
        ]);
    }
}
