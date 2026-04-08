<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the media linker application service.
 */
final class MediaLinker
{
    /**
     * @param array<string,mixed> $category
     *
     * @return array<string,mixed>
     */
    public function link(array $category, string $iconUrl): array
    {
        $category['icon_url'] = $iconUrl;

        return $category;
    }
}
