<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Enums;

enum PurchaseInvoiceStatus: string
{
    case New = 'New';
    case Seen = 'Seen';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
    case Cancelled = 'Cancelled';
    case Storno = 'Storno';
    case UnsuccesfullyDelivered = 'UnsuccesfullyDelivered';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Nova',
            self::Seen => 'Pregledana',
            self::Approved => 'Odobrena',
            self::Rejected => 'Odbijena',
            self::Cancelled => 'Otkazana',
            self::Storno => 'Stornirana',
            self::UnsuccesfullyDelivered => 'Neuspešno isporučena',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Approved,
            self::Rejected,
            self::Cancelled,
            self::Storno,
        ]);
    }

    public function requiresAction(): bool
    {
        return in_array($this, [
            self::New,
            self::Seen,
        ]);
    }
}
