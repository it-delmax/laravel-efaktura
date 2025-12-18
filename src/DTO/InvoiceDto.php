<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class InvoiceDto extends BaseDTO
{
    public ?int $invoiceId = null;
    public ?string $invoiceNumber = null;
    public ?string $status = null;
    public ?string $cirInvoiceId = null;
    public ?string $cirStatus = null;
    public ?string $message = null;
    public ?bool $success = null;
}
