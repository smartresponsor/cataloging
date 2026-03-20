<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
*/

namespace App\Service;

final class CloudflarePurger
{
    public function payloadForKeys(array $keys): array
    {
        return ['files' => [], 'tags' => array_values(array_unique($keys))];
    }
}
