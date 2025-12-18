# Laravel eFaktura

Laravel 12 paket za integraciju sa eFaktura API sistemom Ministarstva finansija Republike Srbije.

## Zahtevi

- PHP 8.2+
- Laravel 11.x ili 12.x
- Guzzle HTTP Client

## Instalacija

```bash
composer require it-delmax/laravel-efaktura
```

Paket koristi Laravel auto-discovery, pa će se automatski registrovati.

### Publikovanje konfiguracije

```bash
php artisan vendor:publish --tag=efaktura-config
```

### Podešavanje okruženja

Dodajte sledeće u vaš `.env` fajl:

```env
EFAKTURA_API_KEY=vaš-api-ključ
EFAKTURA_ENVIRONMENT=production  # ili "demo" za testiranje

# Opciono
EFAKTURA_TIMEOUT=30
EFAKTURA_LOGGING_ENABLED=false
EFAKTURA_CACHE_ENABLED=true
```

## Korišćenje

### Facade pristup

```php
use ItDelmax\LaravelEfaktura\Facades\Efaktura;
use ItDelmax\LaravelEfaktura\Facades\SalesInvoice;
use ItDelmax\LaravelEfaktura\Facades\PurchaseInvoice;

// Prodajne fakture
$invoice = SalesInvoice::get(12345);
echo $invoice->invoiceNumber;

// Ulazne fakture
$invoice = PurchaseInvoice::get(12345);
PurchaseInvoice::accept(12345, 'Prihvaćeno');

// Preko glavnog Efaktura facade-a
$invoice = Efaktura::salesInvoice()->get(12345);
$companies = Efaktura::publicApi()->getAllCompanies();
```

### Dependency Injection

```php
use ItDelmax\LaravelEfaktura\Services\SalesInvoiceService;
use ItDelmax\LaravelEfaktura\Services\PurchaseInvoiceService;
use ItDelmax\LaravelEfaktura\Services\PublicApiService;

class InvoiceController extends Controller
{
    public function __construct(
        protected SalesInvoiceService $salesInvoice,
        protected PurchaseInvoiceService $purchaseInvoice,
        protected PublicApiService $publicApi
    ) {}

    public function show(int $id)
    {
        $invoice = $this->salesInvoice->get($id);

        return view('invoice.show', compact('invoice'));
    }
}
```

### Korišćenje Interface-a za DI

```php
use ItDelmax\LaravelEfaktura\Contracts\SalesInvoiceServiceInterface;
use ItDelmax\LaravelEfaktura\Contracts\PurchaseInvoiceServiceInterface;

class InvoiceService
{
    public function __construct(
        protected SalesInvoiceServiceInterface $salesInvoice,
        protected PurchaseInvoiceServiceInterface $purchaseInvoice
    ) {}
}
```

## Primeri

### Prodajne fakture (Sales Invoice)

```php
use ItDelmax\LaravelEfaktura\Facades\SalesInvoice;
use ItDelmax\LaravelEfaktura\Enums\SendToCir;
use ItDelmax\LaravelEfaktura\Enums\SalesInvoiceStatus;

// Dohvati fakturu
$invoice = SalesInvoice::get(12345);
echo $invoice->invoiceNumber;
echo $invoice->payableAmount;
echo $invoice->accountingCustomerParty->name;

// Upload UBL fajla
$result = SalesInvoice::uploadUbl(
    storage_path('invoices/invoice.xml'),
    'request-123',
    SendToCir::Yes
);

// Import XML sadržaja
$xml = file_get_contents(storage_path('invoices/invoice.xml'));
$result = SalesInvoice::importUbl($xml);

// Otkaži fakturu
SalesInvoice::cancel(12345, 'Razlog otkazivanja');

// Storno fakture
SalesInvoice::storno(12345, 'Razlog storniranja');

// Preuzmi PDF
$pdf = SalesInvoice::getPdf(12345);
Storage::put('invoice.pdf', $pdf);

// Ili direktno sačuvaj
SalesInvoice::downloadPdf(12345, storage_path('invoices/12345.pdf'));

// Dohvati promene statusa
$changes = SalesInvoice::getChanges(now()->subDay());
foreach ($changes as $change) {
    logger()->info("{$change->invoiceNumber}: {$change->status}");
}

// Dohvati fakture po statusu
$ids = SalesInvoice::getIds(
    SalesInvoiceStatus::Approved->value,
    now()->startOfMonth(),
    now()->endOfMonth()
);

// Razlozi oslobođenja od PDV-a (keširano)
$reasons = SalesInvoice::getVatExemptionReasons();
```

### Ulazne fakture (Purchase Invoice)

```php
use ItDelmax\LaravelEfaktura\Facades\PurchaseInvoice;
use ItDelmax\LaravelEfaktura\Enums\PurchaseInvoiceStatus;

// Dohvati fakturu
$invoice = PurchaseInvoice::get(12345);

// Prihvati fakturu
PurchaseInvoice::accept(12345, 'Prihvaćeno');

// Odbij fakturu
PurchaseInvoice::reject(12345, 'Odbijeno zbog...');

// Prihvati po CIR ID-u
PurchaseInvoice::acceptByCirId('CIR-123456', 'OK');

// Dohvati nove (neobrađene) fakture
$newInvoices = PurchaseInvoice::getNew();
foreach ($newInvoices as $invoice) {
    // Procesiraj fakturu
    PurchaseInvoice::accept($invoice->purchaseInvoiceId);
}

// Pregled faktura sa filterima
$overview = PurchaseInvoice::getOverview(
    PurchaseInvoiceStatus::New->value,
    now()->subMonth(),
    now()
);

// PDV obrnuta naplata
PurchaseInvoice::recordVatReverseCharge(12345, 2000.00, 'Komentar');

// Cesija
PurchaseInvoice::assignCirInvoice('CIR-123', 'JBKJS-456', 'CONTRACT-789');
PurchaseInvoice::cancelCirAssignment('CIR-123');
```

### Public API

```php
use ItDelmax\LaravelEfaktura\Facades\Efaktura;

// Verzija eFakture
$version = Efaktura::publicApi()->getVersion();

// Sve registrovane kompanije (keširano 24h)
$companies = Efaktura::publicApi()->getAllCompanies();

// Provera da li je kompanija registrovana
$result = Efaktura::publicApi()->checkByPib('123456789');
if ($result->hasAccount && $result->isActive) {
    echo "Kompanija {$result->name} je aktivna";
}

// Jedinice mere (keširano)
$units = Efaktura::publicApi()->getUnitMeasures();

// Pretplata na notifikacije
Efaktura::publicApi()->subscribe();

// Očisti keš
Efaktura::publicApi()->clearCache();
```

## Error Handling

```php
use ItDelmax\LaravelEfaktura\Exceptions\EfakturaException;
use ItDelmax\LaravelEfaktura\Facades\SalesInvoice;

try {
    $invoice = SalesInvoice::get(99999);
} catch (EfakturaException $e) {
    logger()->error('eFaktura error', [
        'message' => $e->getMessage(),
        'status' => $e->getHttpStatusCode(),
        'body' => $e->getResponseBody(),
    ]);

    return back()->with('error', 'Greška pri dohvatanju fakture');
}
```

## Enumi

Paket uključuje PHP 8.1+ enume sa helper metodama:

```php
use ItDelmax\LaravelEfaktura\Enums\SalesInvoiceStatus;
use ItDelmax\LaravelEfaktura\Enums\PurchaseInvoiceStatus;

// Status label na srpskom
echo SalesInvoiceStatus::Approved->label(); // "Odobrena"
echo PurchaseInvoiceStatus::New->label();   // "Nova"

// Provera finalnog statusa
if (SalesInvoiceStatus::Approved->isFinal()) {
    // Faktura je u finalnom statusu
}

// Provera da li zahteva akciju
if (PurchaseInvoiceStatus::New->requiresAction()) {
    // Potrebno prihvatiti ili odbiti
}
```

## Konfiguracija

```php
// config/efaktura.php

return [
    'api_key' => env('EFAKTURA_API_KEY'),

    'environment' => env('EFAKTURA_ENVIRONMENT', 'production'),

    'urls' => [
        'production' => 'https://efaktura.mfin.gov.rs',
        'demo' => 'https://demoefaktura.mfin.gov.rs',
    ],

    'http' => [
        'timeout' => 30,
        'connect_timeout' => 10,
        'retry' => [
            'times' => 3,
            'sleep' => 100,
        ],
    ],

    'logging' => [
        'enabled' => false,
        'channel' => 'stack',
    ],

    'cache' => [
        'enabled' => true,
        'prefix' => 'efaktura_',
        'ttl' => [
            'companies' => 86400,      // 24h
            'unit_measures' => 86400,  // 24h
            'vat_exemptions' => 86400, // 24h
        ],
    ],
];
```

## Scheduler (Subscribe)

eFaktura API zahteva da se svaki dan pozove `subscribe` endpoint da biste primali notifikacije o promenama statusa faktura za sledeći dan.

### Automatski scheduler

Omogućite automatski scheduler u `.env`:

```env
EFAKTURA_SCHEDULER_ENABLED=true
EFAKTURA_SUBSCRIBE_AT=00:05
EFAKTURA_SCHEDULER_LOG=true
```

Paket će automatski registrovati scheduled task koji pokreće `efaktura:subscribe` komandu svaki dan u podešeno vreme.

### Ručno pokretanje

Možete ručno pokrenuti subscribe komandu:

```bash
php artisan efaktura:subscribe
```

### Manualna konfiguracija schedulera

Ako želite više kontrole, možete onemogućiti automatski scheduler i dodati task ručno u `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('efaktura:subscribe')
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->onOneServer()
    ->emailOutputOnFailure('admin@example.com');
```

### .env opcije za scheduler

```env
# Omogući/onemogući automatski scheduler
EFAKTURA_SCHEDULER_ENABLED=true

# Vreme pokretanja (format HH:MM)
EFAKTURA_SUBSCRIBE_AT=00:05

# Logovanje rezultata
EFAKTURA_SCHEDULER_LOG=true
```

## Testiranje

Za testiranje koristite demo okruženje:

```env
EFAKTURA_ENVIRONMENT=demo
```

## Struktura paketa

```
src/
├── Contracts/
│   ├── EfakturaClientInterface.php
│   ├── SalesInvoiceServiceInterface.php
│   └── PurchaseInvoiceServiceInterface.php
├── DTO/
│   ├── BaseDTO.php
│   ├── SimpleSalesInvoiceDto.php
│   ├── SimplePurchaseInvoiceDto.php
│   └── ... (ostali DTO-ovi)
├── Enums/
│   ├── SendToCir.php
│   ├── SalesInvoiceStatus.php
│   ├── PurchaseInvoiceStatus.php
│   └── CirInvoiceStatus.php
├── Exceptions/
│   └── EfakturaException.php
├── Facades/
│   ├── Efaktura.php
│   ├── SalesInvoice.php
│   └── PurchaseInvoice.php
├── Services/
│   ├── EfakturaClient.php
│   ├── SalesInvoiceService.php
│   ├── PurchaseInvoiceService.php
│   └── PublicApiService.php
├── EfakturaManager.php
└── EfakturaServiceProvider.php
```

## Licenca

MIT License
