<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class PartyDto extends BaseDTO
{
    public ?string $companyId = null;
    public ?string $name = null;
    public ?string $pib = null;
    public ?string $mb = null;
    public ?string $jbkjs = null;
    public ?string $streetName = null;
    public ?string $buildingNumber = null;
    public ?string $cityName = null;
    public ?string $postalZone = null;
    public ?string $countryCode = null;
    public ?string $countryName = null;
    public ?string $email = null;
    public ?string $telephone = null;
    public ?string $bankAccount = null;
    public ?string $contactName = null;
    public ?string $contactEmail = null;
    public ?string $contactTelephone = null;
}
