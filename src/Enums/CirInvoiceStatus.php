<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Enums;

enum CirInvoiceStatus: string
{
    case None = 'None';
    case ActiveCir = 'ActiveCir';
    case InvalidCir = 'InvalidCir';
    case CancelledCir = 'CancelledCir';
    case PartiallySettled = 'PartiallySettled';
    case Settled = 'Settled';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Nema',
            self::ActiveCir => 'Aktivna na CIR',
            self::InvalidCir => 'Nevažeća na CIR',
            self::CancelledCir => 'Otkazana na CIR',
            self::PartiallySettled => 'Delimično izmirena',
            self::Settled => 'Izmirena',
        };
    }
}
