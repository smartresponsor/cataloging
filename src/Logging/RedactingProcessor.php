<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Logging;
/**
 * Provides the redacting processor implementation.
 */
final class RedactingProcessor
{
    /**
     * @param array<string, mixed> $record
     *
     * @return array<string, mixed>
     */
    public function __invoke(array $record): array
    {
        $extra = $record['extra'] ?? null;
        if (is_array($extra) && array_key_exists('signature', $extra)) {
            $extra['signature'] = '***redacted***';
            $record['extra'] = $extra;
        }

        $context = $record['context'] ?? null;
        if (is_array($context) && array_key_exists('secret', $context)) {
            $context['secret'] = '***redacted***';
            $record['context'] = $context;
        }

        return $record;
    }
}
