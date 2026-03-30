<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class CategoryRuleEngine
{
    /**
     * @param array<string, list<scalar>|scalar|null> $category
     * @param array<string, scalar|list<scalar>|null> $rules
     */
    public function match(array $category, array $rules): bool
    {
        foreach ($rules as $attribute => $expectedValue) {
            if (!array_key_exists($attribute, $category)) {
                return false;
            }

            if (!$this->matchesRule($category[$attribute], $expectedValue)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param list<scalar>|scalar|null $actualValue
     * @param scalar|list<scalar>|null $expectedValue
     */
    private function matchesRule(array|bool|float|int|string|null $actualValue, array|bool|float|int|string|null $expectedValue): bool
    {
        if (is_array($actualValue)) {
            if (is_array($expectedValue)) {
                foreach ($expectedValue as $expectedItem) {
                    if (in_array($expectedItem, $actualValue, true)) {
                        return true;
                    }
                }

                return false;
            }

            return in_array($expectedValue, $actualValue, true);
        }

        if (is_array($expectedValue)) {
            return in_array($actualValue, $expectedValue, true);
        }

        return $actualValue === $expectedValue;
    }
}
