<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Enums;

enum SendToCir: string
{
    case Yes = 'Yes';
    case No = 'No';
    case Auto = 'Auto';
}
