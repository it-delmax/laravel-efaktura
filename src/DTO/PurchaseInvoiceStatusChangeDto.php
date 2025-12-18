<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class PurchaseInvoiceStatusChangeDto extends BaseDTO
{
    public ?int $purchaseInvoiceId = null;
    public ?string $invoiceNumber = null;
    public ?string $status = null;
    public ?string $cirInvoiceId = null;
    public ?string $cirStatus = null;
    public ?string $statusChangeDate = null;
    public ?string $supplierPib = null;
    public ?string $supplierName = null;
    public ?float $totalAmount = null;
}
