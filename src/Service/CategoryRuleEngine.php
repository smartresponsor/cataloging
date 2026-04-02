<?php

declare(strict_types=1);

namespace App\Service;

final class CategoryRuleEngine
{
    public function match(array $category, array $rules): bool
    {
        if (isset($rules['and']) && is_array($rules['and'])) {
            foreach ($rules['and'] as $group) {
                if (!is_array($group) || !$this->match($category, $group)) {
                    return false;
                }
            }
        }

        if (isset($rules['or']) && is_array($rules['or'])) {
            $matched = false;
            foreach ($rules['or'] as $group) {
                if (is_array($group) && $this->match($category, $group)) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                return false;
            }
        }

        foreach ($rules as $attribute => $expectedValue) {
            if ('and' === $attribute || 'or' === $attribute) {
                continue;
            }
            if (!array_key_exists($attribute, $category)) {
                return false;
            }

            if (!$this->matchesRule($category[$attribute], $expectedValue)) {
                return false;
            }
        }

        return true;
    }

    private function matchesRule(mixed $actualValue, mixed $expectedValue): bool
    {
        if (is_array($expectedValue) && $this->isOperatorMap($expectedValue)) {
            return $this->matchOperators($actualValue, $expectedValue);
        }

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

    private function matchOperators(mixed $actualValue, array $operators): bool
    {
        foreach ($operators as $op => $value) {
            switch ($op) {
                case 'eq':
                    if ($actualValue !== $value) {
                        return false;
                    }
                    break;
                case 'in':
                    if (is_array($actualValue)) {
                        if (!is_array($value)) {
                            return false;
                        }
                        $matched = false;
                        foreach ($value as $candidate) {
                            if (in_array($candidate, $actualValue, true)) {
                                $matched = true;
                                break;
                            }
                        }
                        if (!$matched) {
                            return false;
                        }
                        break;
                    }
                    if (!is_array($value) || !in_array($actualValue, $value, true)) {
                        return false;
                    }
                    break;
                case 'gt':
                    if (!is_numeric($actualValue) || $actualValue <= $value) {
                        return false;
                    }
                    break;
                case 'gte':
                    if (!is_numeric($actualValue) || $actualValue < $value) {
                        return false;
                    }
                    break;
                case 'lt':
                    if (!is_numeric($actualValue) || $actualValue >= $value) {
                        return false;
                    }
                    break;
                case 'lte':
                    if (!is_numeric($actualValue) || $actualValue > $value) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    private function isOperatorMap(array $value): bool
    {
        foreach (array_keys($value) as $key) {
            if (!is_string($key)) {
                return false;
            }
            if (!in_array($key, ['eq', 'in', 'gt', 'gte', 'lt', 'lte'], true)) {
                return false;
            }
        }

        return [] !== $value;
    }
}
