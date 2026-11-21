<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogWebhookClientServiceInterface;
use App\Cataloging\ValueObject\WebhookDispatchMessageRequest;

/**
 * Provides the webhook client application service.
 */
final readonly class CatalogWebhookClientService implements CatalogWebhookClientServiceInterface
{
    private string $secret;
    private int $maxRetry;

    /**
     * Initializes the webhook client service collaborators.
     */
    public function __construct(string $secret, int $maxRetry = 3)
    {
        $this->secret = $secret;
        $this->maxRetry = max(1, $maxRetry);
    }

    /**
     * Handles the send workflow.
     *
     * @throws \JsonException
     */
    public function send(WebhookDispatchMessageRequest $request): bool
    {
        if ('' === trim($request->endpoint)) {
            return false;
        }

        $body = json_encode(
            ['event' => $request->event, 'payload' => $request->payload],
            JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );
        $signature = hash_hmac('sha256', $body, $this->secret);

        for ($attemptIndex = 0; $attemptIndex < $this->maxRetry; ++$attemptIndex) {
            if ($this->dispatch($request->endpoint, $body, $signature)) {
                return true;
            }

            usleep((int) (2 ** $attemptIndex * 100000));
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
        $statusLine = $http_response_header[0] ?? '';

        if (preg_match('/\s(\d{3})\s?/', $statusLine, $matches)) {
            $status = (int) $matches[1];

            return $status >= 200 && $status < 300;
        }

        return false !== $response;
    }
}
