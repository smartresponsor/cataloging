<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Observability;

use Random\RandomException;

/**
 * Provides the otel category processor implementation.
 */
final class OtelCategoryProcessor
{
    /**
     * @param array<string, scalar|null> $context
     *
     * @return array{trace_id:string,span_id:string,ts:string}
     *
     * @throws RandomException
     * @throws \JsonException
     */
    public function process(array $context): array
    {
        $traceIdValue = $context['trace_id'] ?? null;
        $spanIdValue = $context['span_id'] ?? null;

        $traceId = null !== $traceIdValue ? (string) $traceIdValue : bin2hex(random_bytes(8));
        $spanId = null !== $spanIdValue ? (string) $spanIdValue : bin2hex(random_bytes(4));
        $row = [
            'trace_id' => $traceId,
            'span_id' => $spanId,
            'ts' => date(DATE_ATOM),
        ];
        file_put_contents('report/category-observability-probe.json',
            json_encode(
                $row,
                JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR,
            ),
        );

        return $row;
    }
}
