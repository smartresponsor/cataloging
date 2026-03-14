<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
*/

namespace App\Service\Integration\Category;

final class CloudflarePurger
{
    public function payloadForKeys(array $keys): array
    {
        return ['files' => [], 'tags' => array_values(array_unique($keys))];
    }
}
