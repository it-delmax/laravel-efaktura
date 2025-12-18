<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use ItDelmax\LaravelEfaktura\Console\Commands\SubscribeCommand;
use ItDelmax\LaravelEfaktura\Contracts\EfakturaClientInterface;
use ItDelmax\LaravelEfaktura\Contracts\PurchaseInvoiceServiceInterface;
use ItDelmax\LaravelEfaktura\Contracts\SalesInvoiceServiceInterface;
use ItDelmax\LaravelEfaktura\Services\EfakturaClient;
use ItDelmax\LaravelEfaktura\Services\PublicApiService;
use ItDelmax\LaravelEfaktura\Services\PurchaseInvoiceService;
use ItDelmax\LaravelEfaktura\Services\SalesInvoiceService;

class EfakturaServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->mergeConfigFrom(
      __DIR__ . '/../config/efaktura.php',
      'efaktura'
    );

    // Register the main manager as singleton
    $this->app->singleton(EfakturaManager::class, function (Application $app) {
      return new EfakturaManager($app['config']['efaktura']);
    });

    // Register the HTTP client
    $this->app->singleton(EfakturaClientInterface::class, function (Application $app) {
      return $app->make(EfakturaManager::class)->getClient();
    });

    // Register individual services
    $this->app->singleton(SalesInvoiceServiceInterface::class, function (Application $app) {
      return $app->make(EfakturaManager::class)->salesInvoice();
    });

    $this->app->singleton(SalesInvoiceService::class, function (Application $app) {
      return $app->make(EfakturaManager::class)->salesInvoice();
    });

    $this->app->singleton(PurchaseInvoiceServiceInterface::class, function (Application $app) {
      return $app->make(EfakturaManager::class)->purchaseInvoice();
    });

    $this->app->singleton(PurchaseInvoiceService::class, function (Application $app) {
      return $app->make(EfakturaManager::class)->purchaseInvoice();
    });

    $this->app->singleton(PublicApiService::class, function (Application $app) {
      return $app->make(EfakturaManager::class)->publicApi();
    });

    // Alias for easy access
    $this->app->alias(EfakturaManager::class, 'efaktura');
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    if ($this->app->runningInConsole()) {
      // Publish config file
      $this->publishes([
        __DIR__ . '/../config/efaktura.php' => config_path('efaktura.php'),
      ], 'efaktura-config');

      // Register commands
      $this->commands([
        SubscribeCommand::class,
      ]);
    }

    // Register scheduled tasks
    $this->registerScheduledTasks();
  }

  /**
   * Register scheduled tasks if enabled in config.
   */
  protected function registerScheduledTasks(): void
  {
    $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
      if (! config('efaktura.scheduler.enabled', false)) {
        return;
      }

      $subscribeAt = config('efaktura.scheduler.subscribe_at', '00:05');

      $schedule->command('efaktura:subscribe')
        ->dailyAt($subscribeAt)
        ->withoutOverlapping()
        ->onOneServer()
        ->runInBackground();
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array<int, string>
   */
  public function provides(): array
  {
    return [
      EfakturaManager::class,
      EfakturaClientInterface::class,
      SalesInvoiceServiceInterface::class,
      SalesInvoiceService::class,
      PurchaseInvoiceServiceInterface::class,
      PurchaseInvoiceService::class,
      PublicApiService::class,
      'efaktura',
    ];
  }
}
