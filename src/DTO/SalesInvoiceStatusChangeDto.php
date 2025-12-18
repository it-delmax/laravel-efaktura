<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class SalesInvoiceStatusChangeDto extends BaseDTO
{
    public ?int $salesInvoiceId = null;
    public ?string $invoiceNumber = null;
    public ?string $status = null;
    public ?string $cirInvoiceId = null;
    public ?string $cirStatus = null;
    public ?string $statusChangeDate = null;
    public ?string $buyerPib = null;
    public ?string $buyerName = null;
    public ?float $totalAmount = null;
}
