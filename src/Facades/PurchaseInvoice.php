<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Facades;

use DateTimeInterface;
use Illuminate\Support\Facades\Facade;
use ItDelmax\LaravelEfaktura\Contracts\PurchaseInvoiceServiceInterface;
use ItDelmax\LaravelEfaktura\DTO\AcceptRejectResponseDto;
use ItDelmax\LaravelEfaktura\DTO\PurchaseInvoiceDto;
use ItDelmax\LaravelEfaktura\DTO\PurchaseInvoicesDto;
use ItDelmax\LaravelEfaktura\DTO\SimplePurchaseInvoiceDto;

/**
 * @method static SimplePurchaseInvoiceDto get(int $invoiceId)
 * @method static string getPdf(int $invoiceId)
 * @method static bool downloadPdf(int $invoiceId, string $savePath)
 * @method static string getXml(int $invoiceId)
 * @method static bool downloadXml(int $invoiceId, string $savePath)
 * @method static string getUblByCirInvoiceId(string $cirInvoiceId)
 * @method static string getSignature(int $invoiceId)
 * @method static AcceptRejectResponseDto acceptReject(int $invoiceId, bool $accepted, ?string $comment = null)
 * @method static AcceptRejectResponseDto accept(int $invoiceId, ?string $comment = null)
 * @method static AcceptRejectResponseDto reject(int $invoiceId, ?string $comment = null)
 * @method static AcceptRejectResponseDto acceptRejectByCirInvoiceId(string $cirInvoiceId, bool $accepted, ?string $comment = null)
 * @method static AcceptRejectResponseDto acceptByCirId(string $cirInvoiceId, ?string $comment = null)
 * @method static AcceptRejectResponseDto rejectByCirId(string $cirInvoiceId, ?string $comment = null)
 * @method static array getChanges(DateTimeInterface $date)
 * @method static PurchaseInvoicesDto getIds(?string $status = null, ?DateTimeInterface $dateFrom = null, ?DateTimeInterface $dateTo = null)
 * @method static array getOverview(?string $status = null, ?DateTimeInterface $dateFrom = null, ?DateTimeInterface $dateTo = null)
 * @method static array getNew(?DateTimeInterface $dateFrom = null, ?DateTimeInterface $dateTo = null)
 * @method static array recordVatReverseCharge(int $purchaseInvoiceId, float $vatAmount, ?string $comment = null)
 * @method static PurchaseInvoiceDto assignCirInvoice(string $cirInvoiceId, ?string $assignerPartyJBKJS = null, ?string $assignationContractNumber = null)
 * @method static PurchaseInvoiceDto cancelCirAssignment(string $cirInvoiceId)
 *
 * @see \ItDelmax\LaravelEfaktura\Services\PurchaseInvoiceService
 */
class PurchaseInvoice extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return PurchaseInvoiceServiceInterface::class;
    }
}
