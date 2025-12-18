<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Contracts;

use DateTimeInterface;
use ItDelmax\LaravelEfaktura\DTO\InvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\MiniInvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\SalesInvoicesDto;
use ItDelmax\LaravelEfaktura\DTO\SimpleSalesInvoiceDto;
use ItDelmax\LaravelEfaktura\Enums\SendToCir;

interface SalesInvoiceServiceInterface
{
    public function get(int $invoiceId): SimpleSalesInvoiceDto;

    public function uploadUbl(
        string $ublFilePath,
        ?string $requestId = null,
        ?SendToCir $sendToCir = null,
        bool $executeValidation = true
    ): MiniInvoiceDto;

    public function importUbl(
        string $ublXmlContent,
        ?string $requestId = null,
        ?SendToCir $sendToCir = null,
        bool $executeValidation = true
    ): MiniInvoiceDto;

    public function deleteMultiple(array $invoiceIds): array;

    public function delete(int $invoiceId): int;

    public function cancel(int $invoiceId, ?string $cancelComments = null): InvoiceDto;

    public function storno(int $invoiceId, ?string $stornoReason = null): InvoiceDto;

    public function getPdf(int $invoiceId): string;

    public function getXml(int $invoiceId): string;

    public function getSignature(int $invoiceId): string;

    public function getChanges(DateTimeInterface $date): array;

    public function getIds(
        ?string $status = null,
        ?DateTimeInterface $dateFrom = null,
        ?DateTimeInterface $dateTo = null
    ): SalesInvoicesDto;

    public function getVatExemptionReasons(): array;
}
