<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class CloudflarePurger
{
    public function payloadForKeys(array $keys): array
    {
        return ['files' => [], 'tags' => array_values(array_unique($keys))];
    }
}
