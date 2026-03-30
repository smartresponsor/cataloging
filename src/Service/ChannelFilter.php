<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class ChannelFilter
{
    /**
     * @param list<array<string,mixed>> $categories
     *
     * @return list<array<string,mixed>>
     */
    public function filter(array $categories, string $channel): array
    {
        return array_values(array_filter($categories, static function (array $category) use ($channel): bool {
            return ($category['channel'] ?? 'default') === $channel;
        }));
    }
}
