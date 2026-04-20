<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Request;

use App\Cataloging\Request\Support\RequestValueNormalizer;

/**
 * Provides the webhook dispatch request implementation.
 */
final readonly class WebhookDispatchRequest
{
    /**
     * @param array<string,mixed> $payload
     * @param list<string>        $errors
     */
    public function __construct(
        public string $event,
        public string $endpoint,
        public array $payload,
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

        $event = RequestValueNormalizer::trimmedStringOrDefault($decoded['event'] ?? null, 'category.updated');
        if ('category.updated' === $event && array_key_exists('event', $decoded) && !is_scalar($decoded['event'])) {
            $errors[] = 'event must be a non-empty string';
        }

        $endpoint = RequestValueNormalizer::trimmedStringOrDefault($decoded['endpoint'] ?? null, 'http://localhost:8081/hook');
        if ('http://localhost:8081/hook' === $endpoint && array_key_exists('endpoint', $decoded) && !is_scalar($decoded['endpoint'])) {
            $errors[] = 'endpoint must be a non-empty string';
        }

        $payload = $decoded['payload'] ?? ['id' => 1];
        if (!is_array($payload)) {
            $errors[] = 'payload must be an array';
            $payload = ['id' => 1];
        }

        return new self($event, $endpoint, self::normalizeMap($payload), $errors);
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

    /**
     * @param array<int|string,mixed> $input
     *
     * @return array<string,mixed>
     */
    private static function normalizeMap(array $input): array
    {
        $normalized = [];
        foreach ($input as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }
}
