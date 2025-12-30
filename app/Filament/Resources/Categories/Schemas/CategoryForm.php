<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Category')
                ->schema([
                    Section::make('Translations')
                        ->columns(2)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('name.ar')
                                ->label('Name (AR)')
                                ->placeholder('اسم التصنيف')
                                ->required()
                                ->maxLength(255)
                                ->lazy(),

                            TextInput::make('name.en')
                                ->label('Name (EN)')
                                ->placeholder('Category name')
                                ->required()
                                ->maxLength(255)
                                ->lazy(),

                            Textarea::make('description.ar')
                                ->label('Description (AR)')
                                ->placeholder('وصف التصنيف')
                                ->rows(4)
                                ->columnSpan(1),

                            Textarea::make('description.en')
                                ->label('Description (EN)')
                                ->placeholder('Category description')
                                ->rows(4)
                                ->columnSpan(1),
                        ]),

                    Section::make('Details')
                        ->schema([
                            TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->placeholder('0')
                                ->helperText('Lower number appears first.')
                                ->numeric()
                                ->default(0),

                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    Section::make('Image')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('cover')
                                ->label('Category Image')
                                ->helperText('Recommended: square image. Max 4MB.')
                                ->collection(Category::MEDIA_COLLECTION_COVER)
                                ->disk('public')
                                ->image()
                                ->imageEditor()
                                ->maxSize(4096),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
