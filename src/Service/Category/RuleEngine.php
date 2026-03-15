<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Category;

final class RuleEngine
{
    public function match(array $category, array $rules): bool
    {
        foreach ($rules as $rule => $value) {
            if (!isset($category[$rule])) {
                return false;
            }
            if (is_array($value)) {
                if (!in_array($category[$rule], $value, true)) {
                    return false;
                }
            } elseif ($category[$rule] !== $value) {
                return false;
            }
        }

        return true;
    }
}
