<?php

declare(strict_types=1);

namespace App\Cataloging\Observability;

/**
 * Provides the monolog correlation processor implementation.
 */
final readonly class MonologCorrelationProcessor
{
    /**
     * Initializes the monolog correlation processor service collaborators.
     */
    public function __construct(private RequestCorrelationIdProvider $requestCorrelationIdProvider)
    {
    }

    /**
     * @param array<string, mixed> $record
     *
     * @return array<string, mixed>
     */
    public function __invoke(array $record): array
    {
        $correlationId = $this->requestCorrelationIdProvider->current();
        if (null === $correlationId || '' === $correlationId) {
            return $record;
        }

        $context = $record['context'] ?? [];
        if (!is_array($context)) {
            $context = [];
        }

        if (!array_key_exists('correlation_id', $context)) {
            $context['correlation_id'] = $correlationId;
        }

        $extra = $record['extra'] ?? [];
        if (!is_array($extra)) {
            $extra = [];
        }

        if (!array_key_exists('correlation_id', $extra)) {
            $extra['correlation_id'] = $correlationId;
        }

        $record['context'] = $context;
        $record['extra'] = $extra;

        return $record;
    }
}
