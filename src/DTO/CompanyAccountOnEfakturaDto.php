<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class CompanyAccountOnEfakturaDto extends BaseDTO
{
    public ?bool $hasAccount = null;
    public ?bool $isActive = null;
    public ?string $companyId = null;
    public ?string $pib = null;
    public ?string $mb = null;
    public ?string $jbkjs = null;
    public ?string $name = null;
}
