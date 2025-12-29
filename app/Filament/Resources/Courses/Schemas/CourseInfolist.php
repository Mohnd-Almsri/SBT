<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Course;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Course')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('category')
                        ->label('Category')
                        ->getStateUsing(fn (Course $record) =>
                            $record->category?->getTranslation('name', app()->getLocale()) ?? '-'
                        ),

                    IconEntry::make('is_active')
                        ->label('Active')
                        ->boolean(),
                    IconEntry::make('is_featured')
                        ->label('Featured')
                        ->boolean(),


                    TextEntry::make('title_ar')
                        ->label('Title (AR)')
                        ->getStateUsing(fn (Course $record) =>
                            $record->getTranslation('title', 'ar') ?? '-'
                        ),

                    TextEntry::make('title_en')
                        ->label('Title (EN)')
                        ->getStateUsing(fn (Course $record) =>
                            $record->getTranslation('title', 'en') ?? '-'
                        ),

                    TextEntry::make('description_ar')
                        ->label('Description (AR)')
                        ->getStateUsing(fn (Course $record) =>
                            $record->getTranslation('description', 'ar') ?? '-'
                        )
                        ->prose(),

                    TextEntry::make('description_en')
                        ->label('Description (EN)')
                        ->getStateUsing(fn (Course $record) =>
                            $record->getTranslation('description', 'en') ?? '-'
                        )
                        ->prose(),

                    TextEntry::make('duration_hours')
                        ->label('Duration')
                        ->suffix(' h')
                        ->numeric()
                        ->placeholder('-'),
                ]),

            Section::make('Runs Summary')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('course_runs_count')
                        ->label('Total Runs')
                        ->numeric(),

                    TextEntry::make('next_run_starts_at')
                        ->label('Next Run')
                        ->dateTime()
                        ->placeholder('-'),
                ]),

            Section::make('Media')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    ImageEntry::make('cover')
                        ->label('Cover')
                        ->getStateUsing(fn (Course $record) =>
                        $record->getFirstMediaUrl(Course::MEDIA_COLLECTION_COVER) ?: null
                        )
                        ->url(fn (?string $state) => $state)
                        ->openUrlInNewTab()
                        ->height(180)
                        ->visible(fn (?string $state) => filled($state)),

                    RepeatableEntry::make('gallery')
                        ->label('Gallery')
                        ->columnSpanFull()
                        ->contained(false)
                        ->schema([
                            ImageEntry::make('url')
                                ->hiddenLabel()
                                ->height(180)
                                ->url(fn (?string $state) => $state)
                                ->openUrlInNewTab(),
                        ])
                        ->getStateUsing(fn (Course $record) =>
                        $record->getMedia(Course::MEDIA_COLLECTION_GALLERY)
                            ->map(fn ($media) => ['url' => $media->getUrl()])
                            ->values()
                            ->all()
                        )
                        ->visible(fn (Course $record) =>
                        $record->getMedia(Course::MEDIA_COLLECTION_GALLERY)->isNotEmpty()
                        ),
                ]),

            Section::make('Timestamps')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Created At')
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label('Updated At')
                        ->dateTime(),
                ]),
        ]);
    }
}
