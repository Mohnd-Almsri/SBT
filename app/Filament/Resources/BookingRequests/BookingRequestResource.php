<?php

namespace App\Filament\Resources\BookingRequests;

use App\Filament\Resources\BookingRequests\Pages\EditBookingRequest;
use App\Filament\Resources\BookingRequests\Pages\ListBookingRequests;
use App\Filament\Resources\BookingRequests\Pages\ViewBookingRequest;
use App\Filament\Resources\BookingRequests\Schemas\BookingRequestForm;
use App\Filament\Resources\BookingRequests\Schemas\BookingRequestInfolist;
use App\Filament\Resources\BookingRequests\Tables\BookingRequestsTable;
use App\Models\BookingRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookingRequestResource extends Resource
{
    protected static ?string $model = BookingRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    // recordTitleAttribute لازم يكون عمود فعلي، نحذفه ونخلّي العنوان بالـ table/infolist
    // protected static ?string $recordTitleAttribute = 'BookingRequests';

    protected static ?string $navigationLabel = 'Booking Requests';
    protected static ?string $modelLabel = 'Booking Request';
    protected static ?string $pluralModelLabel = 'Booking Requests';

    public static function form(Schema $schema): Schema
    {
        // بما إننا ما بدنا create/edit من الأدمن حالياً، غالباً ما رح تُستعمل
        // بس خلّينا نخليها موجودة إذا احتجتها لاحقاً
        return BookingRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BookingRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingRequests::route('/'),
            'view'  => ViewBookingRequest::route('/{record}'),
            // intentionally disabled:
            // 'create' => CreateBookingRequest::route('/create'),
             'edit' => EditBookingRequest::route('/{record}/edit'),
        ];
    }
}
