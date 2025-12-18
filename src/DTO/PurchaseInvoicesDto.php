<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class PurchaseInvoicesDto extends BaseDTO
{
    /** @var int[] */
    public ?array $invoiceIds = null;
}
