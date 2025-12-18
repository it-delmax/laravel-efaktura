<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Contracts;

use DateTimeInterface;
use ItDelmax\LaravelEfaktura\DTO\AcceptRejectResponseDto;
use ItDelmax\LaravelEfaktura\DTO\PurchaseInvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\PurchaseInvoicesDto;
use ItDelmax\LaravelEfaktura\DTO\SimplePurchaseInvoiceDto;

interface PurchaseInvoiceServiceInterface
{
    public function get(int $invoiceId): SimplePurchaseInvoiceDto;

    public function getPdf(int $invoiceId): string;

    public function getXml(int $invoiceId): string;

    public function getUblByCirInvoiceId(string $cirInvoiceId): string;

    public function getSignature(int $invoiceId): string;

    public function acceptReject(int $invoiceId, bool $accepted, ?string $comment = null): AcceptRejectResponseDto;

    public function accept(int $invoiceId, ?string $comment = null): AcceptRejectResponseDto;

    public function reject(int $invoiceId, ?string $comment = null): AcceptRejectResponseDto;

    public function acceptRejectByCirInvoiceId(
        string $cirInvoiceId,
        bool $accepted,
        ?string $comment = null
    ): AcceptRejectResponseDto;

    public function getChanges(DateTimeInterface $date): array;

    public function getIds(
        ?string $status = null,
        ?DateTimeInterface $dateFrom = null,
        ?DateTimeInterface $dateTo = null
    ): PurchaseInvoicesDto;

    public function getOverview(
        ?string $status = null,
        ?DateTimeInterface $dateFrom = null,
        ?DateTimeInterface $dateTo = null
    ): array;

    public function recordVatReverseCharge(
        int $purchaseInvoiceId,
        float $vatAmount,
        ?string $comment = null
    ): array;

    public function assignCirInvoice(
        string $cirInvoiceId,
        ?string $assignerPartyJBKJS = null,
        ?string $assignationContractNumber = null
    ): PurchaseInvoiceDto;

    public function cancelCirAssignment(string $cirInvoiceId): PurchaseInvoiceDto;
}
