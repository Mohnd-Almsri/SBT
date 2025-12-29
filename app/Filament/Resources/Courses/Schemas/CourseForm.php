<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Category;
use App\Models\Course;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Group::make()
                ->schema([
                    Section::make('Course')
                        ->schema([
                            Section::make('Titles')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('title.ar')
                                        ->label('Title (AR)')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('title.en')
                                        ->label('Title (EN)')
                                        ->required()
                                        ->maxLength(255),
                                ]),

                            Section::make('Description')
                                ->columns(2)
                                ->schema([
                                    Textarea::make('description.ar')
                                        ->label('Description (AR)')
                                        ->rows(6)
                                        ->nullable(),

                                    Textarea::make('description.en')
                                        ->label('Description (EN)')
                                        ->rows(6)
                                        ->nullable(),
                                ]),

                            Section::make('Details')
                                ->columns(2)
                                ->schema([
                                    Select::make('category_id')
                                        ->label('Category')
                                        ->relationship(
                                            name: 'category',
                                            titleAttribute: 'name', // حتى لو JSON، الليبل الحقيقي جاي من callback
                                            modifyQueryUsing: fn (Builder $query) => $query
                                                ->select(['id', 'name', 'slug'])
//                                                ->where('is_active', true) // اختياري: بس الفئات الفعالة
                                                ->orderByDesc('id')
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->getOptionLabelFromRecordUsing(function (Model $record): string {
                                            /** @var Category $record */
                                            return $record->getTranslation('name', app()->getLocale()) ?: ($record->slug ?? "#{$record->id}");
                                        })
                                        ->required(),

                                    TextInput::make('duration_hours')
                                        ->label('Duration (hours)')
                                        ->numeric()
                                        ->minValue(1)
                                        ->suffix('h'),

                                    Toggle::make('is_active')
                                        ->label('Active')
                                        ->default(true),

                                    Toggle::make('is_featured')
                                        ->label('Featured')
                                        ->default(false),
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Group::make()
                ->schema([
                    Section::make('Media')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('cover')
                                ->label('Cover')
                                ->helperText('Upload a single cover image . Max size: 4MB.')
                                ->collection(Course::MEDIA_COLLECTION_COVER)
                                ->disk('public')
                                ->image()
                                ->imageEditor()
                                ->maxSize(4096),

                            SpatieMediaLibraryFileUpload::make('gallery')
                                ->label('Gallery')
                                ->helperText('Upload multiple images for the gallery. You can reorder them. Max size per image: 4MB.')
                                ->collection(Course::MEDIA_COLLECTION_GALLERY)
                                ->disk('public')
                                ->image()
                                ->multiple()
                                ->imageEditor()
                                ->reorderable()
                                ->maxSize(4096),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}
