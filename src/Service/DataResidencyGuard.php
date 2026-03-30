<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class DataResidencyGuard
{
    /**
     * @param array<string,mixed>                    $category
     * @param array{forbidden_fields?: list<string>} $policy
     *
     * @return array<string,mixed>
     */
    public function filter(array $category, array $policy): array
    {
        foreach ($policy['forbidden_fields'] ?? [] as $field) {
            unset($category[$field]);
        }

        return $category;
    }
}
