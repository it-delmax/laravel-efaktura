<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Contracts;

interface EfakturaClientInterface
{
    public function get(string $endpoint, array $query = [], array $headers = []): array;

    public function post(string $endpoint, array $data = [], array $query = [], array $headers = []): array;

    public function postXml(string $endpoint, string $xml, array $query = [], array $headers = []): array;

    public function postMultipart(string $endpoint, array $multipart, array $query = [], array $headers = []): array;

    public function put(string $endpoint, array $data = [], array $query = [], array $headers = []): array;

    public function delete(string $endpoint, array $data = [], array $headers = []): array;

    public function getFile(string $endpoint, array $query = [], array $headers = []): string;

    public function getApiKey(): string;

    public function getBaseUrl(): string;
}
