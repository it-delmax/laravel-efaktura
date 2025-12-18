<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Services;

use DateTimeInterface;
use ItDelmax\LaravelEfaktura\Contracts\EfakturaClientInterface;
use ItDelmax\LaravelEfaktura\Contracts\PurchaseInvoiceServiceInterface;
use ItDelmax\LaravelEfaktura\DTO\AcceptRejectResponseDto;
use ItDelmax\LaravelEfaktura\DTO\PurchaseInvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\PurchaseInvoiceOverviewDto;
use ItDelmax\LaravelEfaktura\DTO\PurchaseInvoicesDto;
use ItDelmax\LaravelEfaktura\DTO\PurchaseInvoiceStatusChangeDto;
use ItDelmax\LaravelEfaktura\DTO\SimplePurchaseInvoiceDto;
use ItDelmax\LaravelEfaktura\Exceptions\EfakturaException;

class PurchaseInvoiceService implements PurchaseInvoiceServiceInterface
{
    public function __construct(
        protected EfakturaClientInterface $client,
        protected array $cacheConfig = []
    ) {}

    /**
     * Get purchase invoice by ID
     *
     * @throws EfakturaException
     */
    public function get(int $invoiceId): SimplePurchaseInvoiceDto
    {
        $response = $this->client->get('/api/publicApi/purchase-invoice', [
            'invoiceId' => $invoiceId,
        ]);

        return SimplePurchaseInvoiceDto::fromArray($response);
    }

    /**
     * Get purchase invoice PDF
     *
     * @throws EfakturaException
     */
    public function getPdf(int $invoiceId): string
    {
        return $this->client->getFile('/api/publicApi/purchase-invoice/pdf', [
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
     * Get purchase invoice XML (UBL)
     *
     * @throws EfakturaException
     */
    public function getXml(int $invoiceId): string
    {
        return $this->client->getFile('/api/publicApi/purchase-invoice/xml', [
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
     * Get purchase invoice UBL by CIR invoice ID
     *
     * @throws EfakturaException
     */
    public function getUblByCirInvoiceId(string $cirInvoiceId): string
    {
        return $this->client->getFile("/api/publicApi/purchase-invoice/ubl/{$cirInvoiceId}");
    }

    /**
     * Get purchase invoice signature
     *
     * @throws EfakturaException
     */
    public function getSignature(int $invoiceId): string
    {
        return $this->client->getFile('/api/publicApi/purchase-invoice/signature', [
            'invoiceId' => $invoiceId,
        ]);
    }

    /**
     * Accept or reject purchase invoice
     *
     * @throws EfakturaException
     */
    public function acceptReject(int $invoiceId, bool $accepted, ?string $comment = null): AcceptRejectResponseDto
    {
        $response = $this->client->post('/api/publicApi/purchase-invoice/acceptRejectPurchaseInvoice', [
            'invoiceId' => $invoiceId,
            'accepted' => $accepted,
            'comment' => $comment,
        ]);

        return AcceptRejectResponseDto::fromArray($response);
    }

    /**
     * Accept purchase invoice
     *
     * @throws EfakturaException
     */
    public function accept(int $invoiceId, ?string $comment = null): AcceptRejectResponseDto
    {
        return $this->acceptReject($invoiceId, true, $comment);
    }

    /**
     * Reject purchase invoice
     *
     * @throws EfakturaException
     */
    public function reject(int $invoiceId, ?string $comment = null): AcceptRejectResponseDto
    {
        return $this->acceptReject($invoiceId, false, $comment);
    }

    /**
     * Accept or reject purchase invoice by CIR invoice ID
     *
     * @throws EfakturaException
     */
    public function acceptRejectByCirInvoiceId(
        string $cirInvoiceId,
        bool $accepted,
        ?string $comment = null
    ): AcceptRejectResponseDto {
        $response = $this->client->post(
            '/api/publicApi/purchase-invoice/acceptRejectPurchaseInvoiceByCirInvoiceId',
            [
                'cirInvoiceId' => $cirInvoiceId,
                'accepted' => $accepted,
                'comment' => $comment,
            ]
        );

        return AcceptRejectResponseDto::fromArray($response);
    }

    /**
     * Accept purchase invoice by CIR ID
     *
     * @throws EfakturaException
     */
    public function acceptByCirId(string $cirInvoiceId, ?string $comment = null): AcceptRejectResponseDto
    {
        return $this->acceptRejectByCirInvoiceId($cirInvoiceId, true, $comment);
    }

    /**
     * Reject purchase invoice by CIR ID
     *
     * @throws EfakturaException
     */
    public function rejectByCirId(string $cirInvoiceId, ?string $comment = null): AcceptRejectResponseDto
    {
        return $this->acceptRejectByCirInvoiceId($cirInvoiceId, false, $comment);
    }

    /**
     * Get purchase invoices status changes for a specific date
     *
     * @return PurchaseInvoiceStatusChangeDto[]
     * @throws EfakturaException
     */
    public function getChanges(DateTimeInterface $date): array
    {
        $response = $this->client->post('/api/publicApi/purchase-invoice/changes', [], [
            'date' => $date->format('c'),
        ]);

        return PurchaseInvoiceStatusChangeDto::collection($response);
    }

    /**
     * Get purchase invoice IDs by status and date range
     *
     * @throws EfakturaException
     */
    public function getIds(
        ?string $status = null,
        ?DateTimeInterface $dateFrom = null,
        ?DateTimeInterface $dateTo = null
    ): PurchaseInvoicesDto {
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

        $response = $this->client->post('/api/publicApi/purchase-invoice/ids', [], $query);

        return PurchaseInvoicesDto::fromArray($response);
    }

    /**
     * Get purchase invoice overview by status and date range
     *
     * @return PurchaseInvoiceOverviewDto[]
     * @throws EfakturaException
     */
    public function getOverview(
        ?string $status = null,
        ?DateTimeInterface $dateFrom = null,
        ?DateTimeInterface $dateTo = null
    ): array {
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

        $response = $this->client->get('/api/publicApi/purchase-invoice/overview', $query);

        return PurchaseInvoiceOverviewDto::collection($response);
    }

    /**
     * Get new (unprocessed) purchase invoices
     *
     * @return PurchaseInvoiceOverviewDto[]
     * @throws EfakturaException
     */
    public function getNew(?DateTimeInterface $dateFrom = null, ?DateTimeInterface $dateTo = null): array
    {
        return $this->getOverview('New', $dateFrom, $dateTo);
    }

    /**
     * Record VAT reverse charge for purchase invoice
     *
     * @throws EfakturaException
     */
    public function recordVatReverseCharge(
        int $purchaseInvoiceId,
        float $vatAmount,
        ?string $comment = null
    ): array {
        return $this->client->post('/api/publicApi/purchase-invoice/vatReverseCharge', [
            'purchaseInvoiceId' => $purchaseInvoiceId,
            'vatAmount' => $vatAmount,
            'comment' => $comment,
        ]);
    }

    /**
     * Assign CIR invoice
     *
     * @throws EfakturaException
     */
    public function assignCirInvoice(
        string $cirInvoiceId,
        ?string $assignerPartyJBKJS = null,
        ?string $assignationContractNumber = null
    ): PurchaseInvoiceDto {
        $query = [];

        if ($assignerPartyJBKJS !== null) {
            $query['AssignerPartyJBKJS'] = $assignerPartyJBKJS;
        }

        if ($assignationContractNumber !== null) {
            $query['AssignationContractNumber'] = $assignationContractNumber;
        }

        $response = $this->client->post(
            "/api/publicApi/purchase-invoice/{$cirInvoiceId}/assign",
            [],
            $query
        );

        return PurchaseInvoiceDto::fromArray($response);
    }

    /**
     * Cancel CIR invoice assignment
     *
     * @throws EfakturaException
     */
    public function cancelCirAssignment(string $cirInvoiceId): PurchaseInvoiceDto
    {
        $response = $this->client->get("/api/publicApi/purchase-invoice/{$cirInvoiceId}/cancelassign");

        return PurchaseInvoiceDto::fromArray($response);
    }
}
