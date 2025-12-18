<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;
use Throwable;

class EfakturaException extends Exception
{
    protected ?array $responseBody = null;
    protected ?int $httpStatusCode = null;
    protected ?Response $response = null;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?array $responseBody = null,
        ?int $httpStatusCode = null,
        ?Response $response = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseBody = $responseBody;
        $this->httpStatusCode = $httpStatusCode;
        $this->response = $response;
    }

    public static function fromResponse(Response $response, string $message = ''): self
    {
        $body = $response->json() ?? ['raw' => $response->body()];
        $errorMessage = $message ?: self::extractErrorMessage($body);

        return new self(
            $errorMessage,
            $response->status(),
            null,
            $body,
            $response->status(),
            $response
        );
    }

    protected static function extractErrorMessage(array $body): string
    {
        return $body['message']
            ?? $body['error']
            ?? $body['Message']
            ?? (isset($body['errors']) && is_array($body['errors'])
                ? implode(', ', $body['errors'])
                : 'Unknown error');
    }

    public function getResponseBody(): ?array
    {
        return $this->responseBody;
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function context(): array
    {
        return [
            'http_status' => $this->httpStatusCode,
            'response_body' => $this->responseBody,
        ];
    }
}
