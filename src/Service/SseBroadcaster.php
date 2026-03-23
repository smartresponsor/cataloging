<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class SseBroadcaster
{
    public function format(string $event, array $data): string
    {
        return "event: {$event}\n".'data: '.json_encode($data, JSON_UNESCAPED_SLASHES)."\n\n";
    }
}
