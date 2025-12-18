<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ItDelmax\LaravelEfaktura\Exceptions\EfakturaException;
use ItDelmax\LaravelEfaktura\Services\PublicApiService;

class SubscribeCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'efaktura:subscribe
                            {--force : Force subscribe even if scheduler is disabled}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Subscribe to eFaktura notifications for the next day';

  /**
   * Execute the console command.
   */
  public function handle(PublicApiService $publicApi): int
  {
    $this->info('Subscribing to eFaktura notifications...');

    try {
      $result = $publicApi->subscribe();

      $message = "eFaktura subscribe successful: {$result}";

      $this->info($message);

      if (config('efaktura.scheduler.log_results', true)) {
        Log::channel(config('efaktura.logging.channel', 'stack'))
          ->info($message);
      }

      return self::SUCCESS;
    } catch (EfakturaException $e) {
      $errorMessage = "eFaktura subscribe failed: {$e->getMessage()}";

      $this->error($errorMessage);

      if (config('efaktura.scheduler.log_results', true)) {
        Log::channel(config('efaktura.logging.channel', 'stack'))
          ->error($errorMessage, [
            'http_status' => $e->getHttpStatusCode(),
            'response' => $e->getResponseBody(),
          ]);
      }

      return self::FAILURE;
    } catch (\Exception $e) {
      $errorMessage = "eFaktura subscribe error: {$e->getMessage()}";

      $this->error($errorMessage);

      if (config('efaktura.scheduler.log_results', true)) {
        Log::channel(config('efaktura.logging.channel', 'stack'))
          ->error($errorMessage, [
            'exception' => $e,
          ]);
      }

      return self::FAILURE;
    }
  }
}
