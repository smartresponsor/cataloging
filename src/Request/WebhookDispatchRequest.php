<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Request;
/**
 * Provides the webhook dispatch request implementation.
 */
final class WebhookDispatchRequest
{
    /**
     * @param array<mixed> $payload
     * @param list<string> $errors
     */
    public function __construct(
        public readonly string $event,
        public readonly string $endpoint,
        public readonly array $payload,
        private array $errors = [],
    ) {
    }

    public static function fromJson(string $json): self
    {
        if ('' === trim($json)) {
            return new self('category.updated', 'http://localhost:8081/hook', ['id' => 1]);
        }

        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return new self(
                'category.updated',
                'http://localhost:8081/hook',
                ['id' => 1],
                ['payload must be a JSON object'],
            );
        }

        $errors = [];

        $event = $decoded['event'] ?? 'category.updated';
        if (!is_string($event) || '' === trim($event)) {
            $errors[] = 'event must be a non-empty string';
            $event = 'category.updated';
        }

        $endpoint = $decoded['endpoint'] ?? 'http://localhost:8081/hook';
        if (!is_string($endpoint) || '' === trim($endpoint)) {
            $errors[] = 'endpoint must be a non-empty string';
            $endpoint = 'http://localhost:8081/hook';
        }

        $payload = $decoded['payload'] ?? ['id' => 1];
        if (!is_array($payload)) {
            $errors[] = 'payload must be an array';
            $payload = ['id' => 1];
        }

        return new self($event, $endpoint, $payload, $errors);
    }
    /**
     * Determines whether the valid condition is satisfied.
     */
    public function isValid(): bool
    {
        return [] === $this->errors;
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
