<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

class InvoiceLineDto extends BaseDTO
{
    public ?int $lineId = null;
    public ?int $ordinalNumber = null;
    public ?string $itemName = null;
    public ?string $itemDescription = null;
    public ?string $sellersItemIdentification = null;
    public ?string $buyersItemIdentification = null;
    public ?float $invoicedQuantity = null;
    public ?string $unitCode = null;
    public ?string $unitCodeName = null;
    public ?float $lineExtensionAmount = null;
    public ?float $priceAmount = null;
    public ?float $baseQuantity = null;
    public ?float $allowanceChargeAmount = null;
    public ?string $allowanceChargeReason = null;
    public ?bool $allowanceChargeIndicator = null;
    public ?float $taxPercent = null;
    public ?string $taxCategoryId = null;
    public ?string $taxExemptionReasonCode = null;
    public ?string $taxExemptionReason = null;
    public ?string $classifiedTaxCategoryId = null;
    public ?string $classifiedTaxCategoryPercent = null;
    public ?string $note = null;
    public ?string $orderLineReferenceId = null;
    public ?string $documentReferenceId = null;
}
