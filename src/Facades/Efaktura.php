<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Facades;

use Illuminate\Support\Facades\Facade;
use ItDelmax\LaravelEfaktura\EfakturaManager;
use ItDelmax\LaravelEfaktura\Services\PublicApiService;
use ItDelmax\LaravelEfaktura\Services\PurchaseInvoiceService;
use ItDelmax\LaravelEfaktura\Services\SalesInvoiceService;

/**
 * @method static SalesInvoiceService salesInvoice()
 * @method static PurchaseInvoiceService purchaseInvoice()
 * @method static PublicApiService publicApi()
 * @method static bool isDemo()
 * @method static bool isProduction()
 *
 * @see \ItDelmax\LaravelEfaktura\EfakturaManager
 */
class Efaktura extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return EfakturaManager::class;
    }
}
