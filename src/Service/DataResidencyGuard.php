<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class DataResidencyGuard
{
    public function filter(array $category, array $policy): array
    {
        foreach ($policy['forbidden_fields'] ?? [] as $field) {
            if (array_key_exists($field, $category)) {
                unset($category[$field]);
            }
        }

        return $category;
    }
}
