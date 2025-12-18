<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura;

use ItDelmax\LaravelEfaktura\Contracts\EfakturaClientInterface;
use ItDelmax\LaravelEfaktura\Services\EfakturaClient;
use ItDelmax\LaravelEfaktura\Services\PublicApiService;
use ItDelmax\LaravelEfaktura\Services\PurchaseInvoiceService;
use ItDelmax\LaravelEfaktura\Services\SalesInvoiceService;

/**
 * Main entry point for eFaktura API
 *
 * @property-read SalesInvoiceService $salesInvoice
 * @property-read PurchaseInvoiceService $purchaseInvoice
 * @property-read PublicApiService $publicApi
 */
class EfakturaManager
{
  protected EfakturaClientInterface $client;
  protected array $config;

  protected ?SalesInvoiceService $salesInvoiceService = null;
  protected ?PurchaseInvoiceService $purchaseInvoiceService = null;
  protected ?PublicApiService $publicApiService = null;

  public function __construct(array $config)
  {
    $this->config = $config;
    $this->client = $this->createClient();
  }

  protected function createClient(): EfakturaClient
  {
    $baseUrl = $this->config['environment'] === 'demo'
      ? ($this->config['urls']['demo'] ?? EfakturaClient::DEMO_URL)
      : ($this->config['urls']['production'] ?? EfakturaClient::PRODUCTION_URL);

    return new EfakturaClient(
      $this->config['api_key'],
      $baseUrl,
      [
        'timeout' => $this->config['http']['timeout'] ?? 30,
        'connect_timeout' => $this->config['http']['connect_timeout'] ?? 10,
        'retry' => $this->config['http']['retry'] ?? [],
        'logging' => $this->config['logging'] ?? [],
      ]
    );
  }

  /**
   * Get the underlying HTTP client
   */
  public function getClient(): EfakturaClientInterface
  {
    return $this->client;
  }

  /**
   * Get sales invoice service
   */
  public function salesInvoice(): SalesInvoiceService
  {
    if ($this->salesInvoiceService === null) {
      $this->salesInvoiceService = new SalesInvoiceService(
        $this->client,
        $this->config['cache'] ?? []
      );
    }

    return $this->salesInvoiceService;
  }

  /**
   * Get purchase invoice service
   */
  public function purchaseInvoice(): PurchaseInvoiceService
  {
    if ($this->purchaseInvoiceService === null) {
      $this->purchaseInvoiceService = new PurchaseInvoiceService(
        $this->client,
        $this->config['cache'] ?? []
      );
    }

    return $this->purchaseInvoiceService;
  }

  /**
   * Get public API service
   */
  public function publicApi(): PublicApiService
  {
    if ($this->publicApiService === null) {
      $this->publicApiService = new PublicApiService(
        $this->client,
        $this->config['cache'] ?? []
      );
    }

    return $this->publicApiService;
  }

  /**
   * Check if using demo environment
   */
  public function isDemo(): bool
  {
    return $this->config['environment'] === 'demo';
  }

  /**
   * Check if using production environment
   */
  public function isProduction(): bool
  {
    return $this->config['environment'] !== 'demo';
  }

  /**
   * Magic getter for services
   */
  public function __get(string $name): mixed
  {
    return match ($name) {
      'salesInvoice' => $this->salesInvoice(),
      'purchaseInvoice' => $this->purchaseInvoice(),
      'publicApi' => $this->publicApi(),
      default => throw new \InvalidArgumentException("Unknown service: {$name}"),
    };
  }

  /**
   * Magic method caller for service methods
   */
  public function __call(string $name, array $arguments): mixed
  {
    // Proxy calls to salesInvoice if method exists
    if (method_exists($this->salesInvoice(), $name)) {
      return $this->salesInvoice()->$name(...$arguments);
    }

    // Proxy calls to purchaseInvoice if method exists
    if (method_exists($this->purchaseInvoice(), $name)) {
      return $this->purchaseInvoice()->$name(...$arguments);
    }

    // Proxy calls to publicApi if method exists
    if (method_exists($this->publicApi(), $name)) {
      return $this->publicApi()->$name(...$arguments);
    }

    throw new \BadMethodCallException("Method {$name} does not exist.");
  }
}
