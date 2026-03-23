<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class ChannelFilter
{
    public function filter(array $categories, string $channel = 'default'): array
    {
        return array_values(array_filter($categories, static function ($c) use ($channel) {
            return ($c['channel'] ?? 'default') === $channel;
        }));
    }
}
