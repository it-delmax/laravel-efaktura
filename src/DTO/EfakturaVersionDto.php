<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class EfakturaVersionDto extends BaseDTO
{
    public ?string $version = null;
    public ?string $releaseDate = null;
}
