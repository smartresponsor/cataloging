<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class MediaLinker
{
    public function link(array $category, string $iconUrl): array
    {
        $category['icon_url'] = $iconUrl;

        return $category;
    }
}
