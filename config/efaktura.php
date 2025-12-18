<?php

return [

  /*
    |--------------------------------------------------------------------------
    | eFaktura API Key
    |--------------------------------------------------------------------------
    |
    | API ključ koji dobijate od Ministarstva finansija za pristup eFaktura
    | sistemu. Možete ga postaviti u .env fajlu kao EFAKTURA_API_KEY.
    |
    */

  'api_key' => env('EFAKTURA_API_KEY'),

  /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Okruženje za eFaktura API. Podržane vrednosti su:
    | - 'production' - Produkcioni server (efaktura.mfin.gov.rs)
    | - 'demo' - Demo/test server (demoefaktura.mfin.gov.rs)
    |
    */

  'environment' => env('EFAKTURA_ENVIRONMENT', 'production'),

  /*
    |--------------------------------------------------------------------------
    | Base URLs
    |--------------------------------------------------------------------------
    |
    | Bazni URL-ovi za produkciono i demo okruženje. Obično nema potrebe
    | da ih menjate osim ako se ne promene na strani Ministarstva.
    |
    */

  'urls' => [
    'production' => env('EFAKTURA_PRODUCTION_URL', 'https://efaktura.mfin.gov.rs'),
    'demo' => env('EFAKTURA_DEMO_URL', 'https://demoefaktura.mfin.gov.rs'),
  ],

  /*
    |--------------------------------------------------------------------------
    | HTTP Client Options
    |--------------------------------------------------------------------------
    |
    | Opcije za Guzzle HTTP klijent. Možete podesiti timeout, retry
    | logiku i druge opcije specifične za vaše potrebe.
    |
    */

  'http' => [
    'timeout' => env('EFAKTURA_TIMEOUT', 30),
    'connect_timeout' => env('EFAKTURA_CONNECT_TIMEOUT', 10),
    'retry' => [
      'times' => env('EFAKTURA_RETRY_TIMES', 3),
      'sleep' => env('EFAKTURA_RETRY_SLEEP', 100), // milliseconds
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Podešavanja za logovanje API poziva. Korisno za debugging.
    |
    */

  'logging' => [
    'enabled' => env('EFAKTURA_LOGGING_ENABLED', false),
    'channel' => env('EFAKTURA_LOG_CHANNEL', 'stack'),
  ],

  /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Podešavanja za keširanje. Neke informacije kao lista kompanija
    | mogu se keširati za bolje performanse.
    |
    */

  'cache' => [
    'enabled' => env('EFAKTURA_CACHE_ENABLED', true),
    'prefix' => 'efaktura_',
    'ttl' => [
      'companies' => env('EFAKTURA_CACHE_COMPANIES_TTL', 86400), // 24h
      'unit_measures' => env('EFAKTURA_CACHE_UNITS_TTL', 86400), // 24h
      'vat_exemptions' => env('EFAKTURA_CACHE_VAT_TTL', 86400), // 24h
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Scheduler
    |--------------------------------------------------------------------------
    |
    | Podešavanja za automatsko pokretanje scheduled taskova.
    | Subscribe task se mora pokrenuti svaki dan da bi se primale
    | notifikacije o promenama statusa faktura za sledeći dan.
    |
    */

  'scheduler' => [
    // Da li je automatski scheduler omogućen
    'enabled' => env('EFAKTURA_SCHEDULER_ENABLED', false),

    // Vreme pokretanja subscribe komande (format: HH:MM)
    'subscribe_at' => env('EFAKTURA_SUBSCRIBE_AT', '00:05'),

    // Da li da se loguje rezultat subscribe-a
    'log_results' => env('EFAKTURA_SCHEDULER_LOG', true),
  ],

];
