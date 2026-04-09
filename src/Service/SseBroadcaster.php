<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the sse broadcaster application service.
 */
final class SseBroadcaster
{
    /** @param array<string,mixed> $data */
    public function format(string $event, array $data): string
    {
        return "event: {$event}\n".'data: '.json_encode($data, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n\n";
    }
}
