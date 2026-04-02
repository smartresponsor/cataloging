<?php

declare(strict_types=1);

namespace App\Service;

final class RuleNormalizer
{
    /**
     * @param array<mixed> $rules
     *
     * @return array<string, mixed>
     */
    public function normalize(array $rules): array
    {
        $normalized = [];
        foreach ($rules as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if ('and' === $key || 'or' === $key) {
                if (!is_array($value)) {
                    continue;
                }
                $groups = [];
                foreach ($value as $group) {
                    if (is_array($group)) {
                        $groups[] = $this->normalize($group);
                    }
                }
                if ([] !== $groups) {
                    $normalized[$key] = $groups;
                }
                continue;
            }

            if (is_bool($value) || is_float($value) || is_int($value) || is_string($value)) {
                $normalized[$key] = $value;
                continue;
            }
            if (!is_array($value)) {
                continue;
            }
            if ($this->isOperatorMap($value)) {
                $normalizedOperatorMap = [];
                foreach ($value as $operator => $operatorValue) {
                    if (!is_string($operator) || !$this->isSupportedOperator($operator)) {
                        continue;
                    }
                    if (is_bool($operatorValue) || is_float($operatorValue) || is_int($operatorValue) || is_string($operatorValue)) {
                        $normalizedOperatorMap[$operator] = $operatorValue;
                        continue;
                    }
                    if (!is_array($operatorValue)) {
                        continue;
                    }
                    $items = [];
                    foreach ($operatorValue as $item) {
                        if (is_bool($item) || is_float($item) || is_int($item) || is_string($item)) {
                            $items[] = $item;
                        }
                    }
                    $normalizedOperatorMap[$operator] = $items;
                }
                if ([] !== $normalizedOperatorMap) {
                    $normalized[$key] = $normalizedOperatorMap;
                }
                continue;
            }

            $items = [];
            foreach ($value as $item) {
                if (is_bool($item) || is_float($item) || is_int($item) || is_string($item)) {
                    $items[] = $item;
                }
            }
            $normalized[$key] = $items;
        }

        return $normalized;
    }

    /** @param array<mixed> $value */
    private function isOperatorMap(array $value): bool
    {
        foreach (array_keys($value) as $key) {
            if (!is_string($key)) {
                return false;
            }
        }

        return [] !== $value;
    }

    private function isSupportedOperator(string $operator): bool
    {
        return in_array($operator, ['eq', 'in', 'gt', 'gte', 'lt', 'lte'], true);
    }
}
