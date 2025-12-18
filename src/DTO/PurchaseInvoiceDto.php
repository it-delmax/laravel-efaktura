<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class PurchaseInvoiceDto extends BaseDTO
{
    public ?int $purchaseInvoiceId = null;
    public ?string $invoiceNumber = null;
    public ?string $status = null;
    public ?string $cirInvoiceId = null;
    public ?string $cirStatus = null;
    public ?string $assignerPartyJBKJS = null;
    public ?string $assignationContractNumber = null;
    public ?bool $success = null;
    public ?string $message = null;
}
