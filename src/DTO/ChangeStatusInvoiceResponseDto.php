<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class ChangeStatusInvoiceResponseDto extends BaseDTO
{
    public ?string $invoiceNumber = null;
    public ?string $status = null;
}
