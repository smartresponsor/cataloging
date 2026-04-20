<?php

declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Value object describing a webhook payload send request.
 */
final readonly class WebhookPayloadRequest
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(public array $payload)
    {
    }
}
