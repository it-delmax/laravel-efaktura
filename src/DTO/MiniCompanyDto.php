<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class MiniCompanyDto extends BaseDTO
{
    public ?string $companyId = null;
    public ?string $name = null;
    public ?string $pib = null;
    public ?string $mb = null;
    public ?string $jbkjs = null;
    public ?string $address = null;
    public ?string $city = null;
    public ?string $zip = null;
    public ?string $countryCode = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $registrationStatus = null;
}
