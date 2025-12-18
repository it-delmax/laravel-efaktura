<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class MiniInvoiceDto extends BaseDTO
{
    public ?int $salesInvoiceId = null;
    public ?int $purchaseInvoiceId = null;
    public ?string $invoiceNumber = null;
    public ?string $status = null;
    public ?string $cirInvoiceId = null;
    public ?string $cirStatus = null;
    public ?string $requestId = null;
    public ?string $message = null;
    public ?bool $success = null;
}
