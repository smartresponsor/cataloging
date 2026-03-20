<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Logging;

final class RedactingProcessor
{
    public function __invoke(array $record): array
    {
        if (isset($record['extra']['signature'])) {
            $record['extra']['signature'] = '***redacted***';
        }
        if (isset($record['context']['secret'])) {
            $record['context']['secret'] = '***redacted***';
        }

        return $record;
    }
}
