<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the sse broadcaster application service.
 */
final class SseBroadcaster
{
    /**
     * @param string              $event
     * @param array<string,mixed> $data
     *
     * @return string
     *
     * @throws \JsonException
     */
    public function format(string $event, array $data): string
    {
        return 'event: '.$event."\n".'data: '.json_encode($data, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n\n";
    }
}
