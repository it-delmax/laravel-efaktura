<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class TaxSubtotalDto extends BaseDTO
{
    public ?float $taxableAmount = null;
    public ?float $taxAmount = null;
    public ?float $percent = null;
    public ?string $taxCategoryId = null;
    public ?string $taxCategoryName = null;
    public ?string $taxExemptionReasonCode = null;
    public ?string $taxExemptionReason = null;
}
