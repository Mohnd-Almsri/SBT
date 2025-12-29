<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Category')
                ->schema([
                    // بدل Tabs: حقول الترجمة جنب بعض
                    Section::make('Translations')
                        ->columns(2)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('name.ar')
                                ->label('Name (AR)')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, callable $set, callable $get) {
                                    // إذا slug فاضي وما في EN، خليه يتولد من AR كحل احتياطي
                                    if (blank($get('slug')) && blank(data_get($get('name'), 'en')) && filled($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),

                            TextInput::make('name.en')
                                ->label('Name (EN)')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, callable $set) {
                                    // الأفضل توليد slug من الإنكليزي
                                    if (filled($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),

                            // إذا عندك description بالكاتيجوري (JSON)
                            Textarea::make('description.ar')
                                ->label('Description (AR)')
                                ->rows(4)
                                ->columnSpan(1),

                            Textarea::make('description.en')
                                ->label('Description (EN)')
                                ->rows(4)
                                ->columnSpan(1),
                        ]),

                    Section::make('Details')
                        ->schema([
                            TextInput::make('slug')
                                ->label('Slug')
                                ->helperText('URL-friendly identifier. Auto-generated from EN (fallback AR).')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),

                            TextInput::make('sort_order')
                                ->label('Sort Order')
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
