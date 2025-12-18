<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class ValueAddedTaxExemptionReasonDto extends BaseDTO
{
    public ?string $code = null;
    public ?string $description = null;
    public ?string $taxCategory = null;
}
