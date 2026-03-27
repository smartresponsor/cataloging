<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\WebhookClientInterface;

final class WebhookClient implements WebhookClientInterface
{
    private string $secret;
    private int $maxRetry;

    public function __construct(string $secret, int $maxRetry = 3)
    {
        $this->secret = $secret;
        $this->maxRetry = max(1, $maxRetry);
    }

    /** @param array<string,mixed> $payload */
    public function send(string $endpoint, string $event, array $payload): bool
    {
        if ('' === trim($endpoint)) {
            return false;
        }

        $body = json_encode(['event' => $event, 'payload' => $payload], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $body, $this->secret);

        for ($i = 0; $i < $this->maxRetry; ++$i) {
            if ($this->dispatch($endpoint, $body, $signature)) {
                return true;
            }

            usleep((int) (2 ** $i * 100000));
        }

        return false;
    }

    private function dispatch(string $endpoint, string $body, string $signature): bool
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", [
                    'Content-Type: application/json',
                    'X-Webhook-Signature: sha256='.$signature,
                    'Content-Length: '.strlen($body),
                ]),
                'content' => $body,
                'timeout' => 5,
                'ignore_errors' => true,
            ],
        ]);

        $response = file_get_contents($endpoint, false, $context);
        $rawHeaders = $http_response_header;
        /** @var list<string> $headers */
        $headers = is_array($rawHeaders) ? $rawHeaders : [];
        $statusLine = isset($headers[0]) ? (string) $headers[0] : '';

        if (preg_match('/\s(\d{3})\s?/', $statusLine, $matches)) {
            $status = (int) $matches[1];

            return $status >= 200 && $status < 300;
        }

        return false !== $response;
    }
}
