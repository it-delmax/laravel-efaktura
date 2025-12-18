<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Services;

use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use ItDelmax\LaravelEfaktura\Contracts\EfakturaClientInterface;
use ItDelmax\LaravelEfaktura\Contracts\SalesInvoiceServiceInterface;
use ItDelmax\LaravelEfaktura\DTO\InvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\MiniInvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\SalesInvoicesDto;
use ItDelmax\LaravelEfaktura\DTO\SalesInvoiceStatusChangeDto;
use ItDelmax\LaravelEfaktura\DTO\SimpleSalesInvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\ValueAddedTaxExemptionReasonDto;
use ItDelmax\LaravelEfaktura\Enums\SendToCir;
use ItDelmax\LaravelEfaktura\Exceptions\EfakturaException;

class SalesInvoiceService implements SalesInvoiceServiceInterface
{
    public function __construct(
        protected EfakturaClientInterface $client,
        protected array $cacheConfig = []
    ) {}

    /**
     * Get sales invoice by ID
     *
     * @throws EfakturaException
     */
    public function get(int $invoiceId): SimpleSalesInvoiceDto
    {
        $response = $this->client->get('/api/publicApi/sales-invoice', [
            'invoiceId' => $invoiceId,
        ]);

        return SimpleSalesInvoiceDto::fromArray($response);
    }

    /**
     * Find invoice by invoice number
     *
     * @throws EfakturaException
     */
    public function findByNumber(string $invoiceNumber): ?SimpleSalesInvoiceDto
    {
        // API ne podržava direktnu pretragu po broju, moramo tražiti preko IDs
        // Ovo je helper metoda - potrebno je implementirati po potrebi
        return null;
    }

    /**
     * Upload UBL file to create sales invoice
     *
     * @throws EfakturaException
     */
    public function uploadUbl(
        string $ublFilePath,
        ?string $requestId = null,
        ?SendToCir $sendToCir = null,
        bool $executeValidation = true
    ): MiniInvoiceDto {
        $query = $this->buildUblQuery($requestId, $sendToCir, $executeValidation);

        $multipart = [
            [
                'name' => 'ublFile',
                'contents' => fopen($ublFilePath, 'r'),
                'filename' => basename($ublFilePath),
            ],
        ];

        $response = $this->client->postMultipart(
            '/api/publicApi/sales-invoice/ubl/upload',
            $multipart,
            $query
        );

        return MiniInvoiceDto::fromArray($response);
    }

    /**
     * Import UBL XML content directly
     *
     * @throws EfakturaException
     */
    public function importUbl(
        string $ublXmlContent,
        ?string $requestId = null,
        ?SendToCir $sendToCir = null,
        bool $executeValidation = true
    ): MiniInvoiceDto {
        $query = $this->buildUblQuery($requestId, $sendToCir, $executeValidation);

        $response = $this->client->postXml(
            '/api/publicApi/sales-invoice/ubl',
            $ublXmlContent,
            $query
        );

        return MiniInvoiceDto::fromArray($response);
    }

    /**
     * Import UBL from file path (reads content)
     *
     * @throws EfakturaException
     */
    public function importUblFromFile(
        string $filePath,
        ?string $requestId = null,
        ?SendToCir $sendToCir = null,
        bool $executeValidation = true
    ): MiniInvoiceDto {
        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new EfakturaException("Cannot read file: {$filePath}");
        }

        return $this->importUbl($content, $requestId, $sendToCir, $executeValidation);
    }

    protected function buildUblQuery(?string $requestId, ?SendToCir $sendToCir, bool $executeValidation): array
    {
        $query = [];

        if ($requestId !== null) {
            $query['requestId'] = $requestId;
        }

        if ($sendToCir !== null) {
            $query['sendToCir'] = $sendToCir->value;
        }

        $query['executeValidation'] = $executeValidation ? 'true' : 'false';

        return $query;
    }

    /**
     * Delete sales invoices (only draft or new)
     *
     * @param int[] $invoiceIds
     * @return int[] Deleted invoice IDs
     * @throws EfakturaException
     */
    public function deleteMultiple(array $invoiceIds): array
    {
        return $this->client->delete('/api/publicApi/sales-invoice', $invoiceIds);
    }

    /**
     * Delete single sales invoice (only draft or new)
     *
     * @throws EfakturaException
     */
    public function delete(int $invoiceId): int
    {
        $response = $this->client->delete("/api/publicApi/sales-invoice/{$invoiceId}");

        return (int) ($response[0] ?? $invoiceId);
    }

    /**
     * Cancel sales invoice
     *
     * @throws EfakturaException
     */
    public function cancel(int $invoiceId, ?string $cancelComments = null): InvoiceDto
    {
        $response = $this->client->post('/api/publicApi/sales-invoice/cancel', [
            'invoiceId' => $invoiceId,
            'cancelComments' => $cancelComments,
        ]);

        return InvoiceDto::fromArray($response);
    }

    /**
     * Storno sales invoice
     *
     * @throws EfakturaException
     */
    public function storno(int $invoiceId, ?string $stornoReason = null): InvoiceDto
    {
        $response = $this->client->post('/api/publicApi/sales-invoice/storno', [
            'invoiceId' => $invoiceId,
            'stornoReason' => $stornoReason,
        ]);

        return InvoiceDto::fromArray($response);
    }

    /**
     * Get sales invoice PDF
     *
     * @throws EfakturaException
     */
    public function getPdf(int $invoiceId): string
    {
        return $this->client->getFile('/api/publicApi/sales-invoice/pdf', [
            'invoiceId' => $invoiceId,
        ]);
    }

    /**
     * Download PDF and save to file
     *
     * @throws EfakturaException
     */
    public function downloadPdf(int $invoiceId, string $savePath): bool
    {
        $content = $this->getPdf($invoiceId);

        return file_put_contents($savePath, $content) !== false;
    }

    /**
     * Get sales invoice XML (UBL)
     *
     * @throws EfakturaException
     */
    public function getXml(int $invoiceId): string
    {
        return $this->client->getFile('/api/publicApi/sales-invoice/xml', [
            'invoiceId' => $invoiceId,
        ]);
    }

    /**
     * Download XML and save to file
     *
     * @throws EfakturaException
     */
    public function downloadXml(int $invoiceId, string $savePath): bool
    {
        $content = $this->getXml($invoiceId);

        return file_put_contents($savePath, $content) !== false;
    }

    /**
     * Get sales invoice signature
     *
     * @throws EfakturaException
     */
    public function getSignature(int $invoiceId): string
    {
        return $this->client->getFile('/api/publicApi/sales-invoice/signature', [
            'invoiceId' => $invoiceId,
        ]);
    }

    /**
     * Get sales invoices status changes for a specific date
     *
     * @return SalesInvoiceStatusChangeDto[]
     * @throws EfakturaException
     */
    public function getChanges(DateTimeInterface $date): array
    {
        $response = $this->client->post('/api/publicApi/sales-invoice/changes', [], [
            'date' => $date->format('c'),
        ]);

        return SalesInvoiceStatusChangeDto::collection($response);
    }

    /**
     * Get sales invoice IDs by status and date range
     *
     * @throws EfakturaException
     */
    public function getIds(
        ?string $status = null,
        ?DateTimeInterface $dateFrom = null,
        ?DateTimeInterface $dateTo = null
    ): SalesInvoicesDto {
        $query = [];

        if ($status !== null) {
            $query['status'] = $status;
        }

        if ($dateFrom !== null) {
            $query['dateFrom'] = $dateFrom->format('c');
        }

        if ($dateTo !== null) {
            $query['dateTo'] = $dateTo->format('c');
        }

        $response = $this->client->post('/api/publicApi/sales-invoice/ids', [], $query);

        return SalesInvoicesDto::fromArray($response);
    }

    /**
     * Get VAT exemption reason list (cached)
     *
     * @return ValueAddedTaxExemptionReasonDto[]
     * @throws EfakturaException
     */
    public function getVatExemptionReasons(): array
    {
        $cacheKey = ($this->cacheConfig['prefix'] ?? 'efaktura_') . 'vat_exemptions';
        $ttl = $this->cacheConfig['ttl']['vat_exemptions'] ?? 86400;

        if ($this->cacheConfig['enabled'] ?? false) {
            return Cache::remember($cacheKey, $ttl, function () {
                return $this->fetchVatExemptionReasons();
            });
        }

        return $this->fetchVatExemptionReasons();
    }

    protected function fetchVatExemptionReasons(): array
    {
        $response = $this->client->get('/api/publicApi/sales-invoice/getValueAddedTaxExemptionReasonList');

        return ValueAddedTaxExemptionReasonDto::collection($response);
    }
}
