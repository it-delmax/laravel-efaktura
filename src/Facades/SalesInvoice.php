<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Facades;

use DateTimeInterface;
use Illuminate\Support\Facades\Facade;
use ItDelmax\LaravelEfaktura\Contracts\SalesInvoiceServiceInterface;
use ItDelmax\LaravelEfaktura\DTO\InvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\MiniInvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\SalesInvoicesDto;
use ItDelmax\LaravelEfaktura\DTO\SimpleSalesInvoiceDto;
use ItDelmax\LaravelEfaktura\Enums\SendToCir;

/**
 * @method static SimpleSalesInvoiceDto get(int $invoiceId)
 * @method static MiniInvoiceDto uploadUbl(string $ublFilePath, ?string $requestId = null, ?SendToCir $sendToCir = null, bool $executeValidation = true)
 * @method static MiniInvoiceDto importUbl(string $ublXmlContent, ?string $requestId = null, ?SendToCir $sendToCir = null, bool $executeValidation = true)
 * @method static MiniInvoiceDto importUblFromFile(string $filePath, ?string $requestId = null, ?SendToCir $sendToCir = null, bool $executeValidation = true)
 * @method static array deleteMultiple(array $invoiceIds)
 * @method static int delete(int $invoiceId)
 * @method static InvoiceDto cancel(int $invoiceId, ?string $cancelComments = null)
 * @method static InvoiceDto storno(int $invoiceId, ?string $stornoReason = null)
 * @method static string getPdf(int $invoiceId)
 * @method static bool downloadPdf(int $invoiceId, string $savePath)
 * @method static string getXml(int $invoiceId)
 * @method static bool downloadXml(int $invoiceId, string $savePath)
 * @method static string getSignature(int $invoiceId)
 * @method static array getChanges(DateTimeInterface $date)
 * @method static SalesInvoicesDto getIds(?string $status = null, ?DateTimeInterface $dateFrom = null, ?DateTimeInterface $dateTo = null)
 * @method static array getVatExemptionReasons()
 *
 * @see \ItDelmax\LaravelEfaktura\Services\SalesInvoiceService
 */
class SalesInvoice extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SalesInvoiceServiceInterface::class;
    }
}
