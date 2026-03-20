<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class MediaLinker
{
    public function link(array $category, string $iconUrl): array
    {
        $category['icon_url'] = $iconUrl;

        return $category;
    }
}
