<?php

namespace App\Enums;

use Filament\Support\Colors\Color;

enum CourseRunStatus: string
{
    case Open = 'open';
    case Closed = 'closed';
    case Cancelled = 'cancelled';
    case Draft = 'draft';

    /**
     * القيمة النصية (للـ validation وغيره)
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Label قابل للعرض بالـ UI
     */
    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
            self::Cancelled => 'Cancelled',
            self::Draft => 'Draft',
        };
    }

    /**
     * لون مناسب لـ Filament (badges / tabs)
     */
    public function color(): string
    {
        return match ($this) {
            self::Open => 'success',
            self::Draft => 'warning',
            self::Closed => 'gray',
            self::Cancelled => 'danger',
        };
    }

    /**
     * خيارات جاهزة للـ Select / Filter
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [
                $status->value => $status->label(),
            ])
            ->toArray();
    }
}
