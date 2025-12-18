<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ItDelmax\LaravelEfaktura\Contracts\EfakturaClientInterface;
use ItDelmax\LaravelEfaktura\Exceptions\EfakturaException;

class EfakturaClient implements EfakturaClientInterface
{
    protected string $apiKey;
    protected string $baseUrl;
    protected array $config;

    public const PRODUCTION_URL = 'https://efaktura.mfin.gov.rs';
    public const DEMO_URL = 'https://demoefaktura.mfin.gov.rs';

    public function __construct(string $apiKey, string $baseUrl, array $config = [])
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->config = $config;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function makeRequest(): PendingRequest
    {
        $request = Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'ApiKey' => $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->timeout($this->config['timeout'] ?? 30)
            ->connectTimeout($this->config['connect_timeout'] ?? 10);

        if (($this->config['retry']['times'] ?? 0) > 0) {
            $request->retry(
                $this->config['retry']['times'],
                $this->config['retry']['sleep'] ?? 100
            );
        }

        return $request;
    }

    protected function logRequest(string $method, string $endpoint, array $data = []): void
    {
        if ($this->config['logging']['enabled'] ?? false) {
            Log::channel($this->config['logging']['channel'] ?? 'stack')
                ->debug("eFaktura API Request: {$method} {$endpoint}", [
                    'data' => $data,
                ]);
        }
    }

    protected function logResponse(Response $response): void
    {
        if ($this->config['logging']['enabled'] ?? false) {
            Log::channel($this->config['logging']['channel'] ?? 'stack')
                ->debug('eFaktura API Response', [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);
        }
    }

    /**
     * @throws EfakturaException
     */
    public function get(string $endpoint, array $query = [], array $headers = []): array
    {
        $this->logRequest('GET', $endpoint, $query);

        $response = $this->makeRequest()
            ->withHeaders($headers)
            ->get($endpoint, $query);

        $this->logResponse($response);

        return $this->handleResponse($response);
    }

    /**
     * @throws EfakturaException
     */
    public function post(string $endpoint, array $data = [], array $query = [], array $headers = []): array
    {
        $this->logRequest('POST', $endpoint, $data);

        $response = $this->makeRequest()
            ->withHeaders($headers)
            ->post($endpoint . $this->buildQueryString($query), $data);

        $this->logResponse($response);

        return $this->handleResponse($response);
    }

    /**
     * @throws EfakturaException
     */
    public function postXml(string $endpoint, string $xml, array $query = [], array $headers = []): array
    {
        $this->logRequest('POST XML', $endpoint);

        $response = $this->makeRequest()
            ->withHeaders(array_merge(['Content-Type' => 'application/xml'], $headers))
            ->withBody($xml, 'application/xml')
            ->post($endpoint . $this->buildQueryString($query));

        $this->logResponse($response);

        return $this->handleResponse($response);
    }

    /**
     * @throws EfakturaException
     */
    public function postMultipart(string $endpoint, array $multipart, array $query = [], array $headers = []): array
    {
        $this->logRequest('POST Multipart', $endpoint);

        $request = $this->makeRequest()->withHeaders($headers);

        foreach ($multipart as $item) {
            if (isset($item['contents']) && is_resource($item['contents'])) {
                $request = $request->attach(
                    $item['name'],
                    $item['contents'],
                    $item['filename'] ?? null
                );
            }
        }

        $response = $request->post($endpoint . $this->buildQueryString($query));

        $this->logResponse($response);

        return $this->handleResponse($response);
    }

    /**
     * @throws EfakturaException
     */
    public function put(string $endpoint, array $data = [], array $query = [], array $headers = []): array
    {
        $this->logRequest('PUT', $endpoint, $data);

        $response = $this->makeRequest()
            ->withHeaders($headers)
            ->put($endpoint . $this->buildQueryString($query), $data);

        $this->logResponse($response);

        return $this->handleResponse($response);
    }

    /**
     * @throws EfakturaException
     */
    public function delete(string $endpoint, array $data = [], array $headers = []): array
    {
        $this->logRequest('DELETE', $endpoint, $data);

        $response = $this->makeRequest()
            ->withHeaders($headers)
            ->delete($endpoint, $data);

        $this->logResponse($response);

        return $this->handleResponse($response);
    }

    /**
     * @throws EfakturaException
     */
    public function getFile(string $endpoint, array $query = [], array $headers = []): string
    {
        $this->logRequest('GET File', $endpoint, $query);

        $response = $this->makeRequest()
            ->withHeaders($headers)
            ->get($endpoint, $query);

        $this->logResponse($response);

        if ($response->failed()) {
            throw EfakturaException::fromResponse($response);
        }

        return $response->body();
    }

    protected function buildQueryString(array $query): string
    {
        if (empty($query)) {
            return '';
        }

        return '?' . http_build_query($query);
    }

    /**
     * @throws EfakturaException
     */
    protected function handleResponse(Response $response): array
    {
        if ($response->failed()) {
            throw EfakturaException::fromResponse($response);
        }

        $body = $response->body();

        if (empty($body)) {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['raw' => $body];
        }

        return $decoded;
    }
}
