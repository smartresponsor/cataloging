<?php

declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Value object describing outbound webhook dispatch input.
 */
final readonly class WebhookDispatchRequest
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public string $endpoint,
        public string $event,
        public array $payload,
    ) {
    }
}
