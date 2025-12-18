<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Enums;

enum SalesInvoiceStatus: string
{
    case Draft = 'Draft';
    case New = 'New';
    case Sent = 'Sent';
    case Delivered = 'Delivered';
    case Mistake = 'Mistake';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
    case Cancelled = 'Cancelled';
    case Storno = 'Storno';
    case Sending = 'Sending';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Nacrt',
            self::New => 'Nova',
            self::Sent => 'Poslata',
            self::Delivered => 'Isporučena',
            self::Mistake => 'Greška',
            self::Approved => 'Odobrena',
            self::Rejected => 'Odbijena',
            self::Cancelled => 'Otkazana',
            self::Storno => 'Stornirana',
            self::Sending => 'U slanju',
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
}
