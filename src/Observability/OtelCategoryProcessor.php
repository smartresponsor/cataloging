<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Observability;

final class OtelCategoryProcessor
{
    public function process(array $context): array
    {
        $traceId = $context['trace_id'] ?? bin2hex(random_bytes(8));
        $spanId = $context['span_id'] ?? bin2hex(random_bytes(4));
        $row = [
            'trace_id' => $traceId,
            'span_id' => $spanId,
            'ts' => date(DATE_ATOM),
        ];
        file_put_contents('report/catalog-observability-probe.json', json_encode($row, JSON_PRETTY_PRINT));

        return $row;
    }
}
