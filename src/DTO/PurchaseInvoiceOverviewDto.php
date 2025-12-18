<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class PurchaseInvoiceOverviewDto extends BaseDTO
{
    public ?int $purchaseInvoiceId = null;
    public ?string $invoiceNumber = null;
    public ?string $invoiceTypeCode = null;
    public ?string $issueDate = null;
    public ?string $dueDate = null;
    public ?string $status = null;
    public ?string $cirInvoiceId = null;
    public ?string $cirStatus = null;
    public ?string $supplierPib = null;
    public ?string $supplierName = null;
    public ?float $taxExclusiveAmount = null;
    public ?float $taxAmount = null;
    public ?float $payableAmount = null;
    public ?string $currencyCode = null;
    public ?string $deliveredDate = null;
    public ?string $seenDate = null;
}
