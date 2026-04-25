<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Rule;

/**
 * Provides the rule evaluator implementation.
 */
final class RuleEvaluator
{
    /**
     * Evaluates a normalized record against a category rule without relying on SQL compilation.
     *
     * @param array<string,mixed> $record
     */
    public function matches(array $record, CategoryRule $rule): bool
    {
        foreach ($rule->spec()['all'] as $condition) {
            $attributeRaw = $condition['attr'] ?? null;
            if (is_scalar($attributeRaw) && '' !== trim((string) $attributeRaw)) {
                $attribute = trim((string) $attributeRaw);
                $operatorRaw = $condition['op'] ?? null;
                $operator = is_scalar($operatorRaw) ? trim((string) $operatorRaw) : '';
                $candidate = $record[$attribute] ?? null;
                $value = $condition['value'] ?? null;

                if ('in' === $operator && is_array($value)) {
                    if (!in_array($candidate, $value, true)) {
                        return false;
                    }

                    continue;
                }

                if ('between' === $operator && is_array($value) && 2 === count($value)) {
                    if (!$this->compareBetween($candidate, $value[0], $value[1])) {
                        return false;
                    }

                    continue;
                }

                if (in_array($operator, ['>', '>=', '<', '<='], true)) {
                    if (!$this->compareScalar($candidate, $operator, $value)) {
                        return false;
                    }

                    continue;
                }

                return false;
            }

            $tagRaw = $condition['tag'] ?? null;
            if (is_scalar($tagRaw)) {
                $tagSet = $record['tag_set'] ?? null;
                if (!is_array($tagSet) || !in_array((string) $tagRaw, array_map(static fn ($value): string => (string) $value, $tagSet), true)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function compareBetween(mixed $candidate, mixed $lowerBound, mixed $upperBound): bool
    {
        $candidateNumeric = $this->normalizeNumeric($candidate);
        $lowerNumeric = $this->normalizeNumeric($lowerBound);
        $upperNumeric = $this->normalizeNumeric($upperBound);

        if (null === $candidateNumeric || null === $lowerNumeric || null === $upperNumeric) {
            return false;
        }

        return $candidateNumeric >= $lowerNumeric && $candidateNumeric <= $upperNumeric;
    }

    private function compareScalar(mixed $candidate, string $operator, mixed $expected): bool
    {
        $candidateNumeric = $this->normalizeNumeric($candidate);
        $expectedNumeric = $this->normalizeNumeric($expected);

        if (null === $candidateNumeric || null === $expectedNumeric) {
            return false;
        }

        return match ($operator) {
            '>' => $candidateNumeric > $expectedNumeric,
            '>=' => $candidateNumeric >= $expectedNumeric,
            '<' => $candidateNumeric < $expectedNumeric,
            '<=' => $candidateNumeric <= $expectedNumeric,
            default => false,
        };
    }

    private function normalizeNumeric(mixed $value): ?float
    {
        if (!is_int($value) && !is_float($value) && !is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    /**
     * @param CategoryRule $rule
     *
     * @return array{sql:string,params:array<string,mixed>}
     *
     * @throws \JsonException
     */
    public function compile(CategoryRule $rule): array
    {
        $where = [];
        $params = [];
        $parameterIndex = 0;
        foreach ($rule->spec()['all'] as $condition) {
            $attributeRaw = $condition['attr'] ?? null;
            if (is_scalar($attributeRaw) && '' !== trim((string) $attributeRaw)) {
                $attribute = trim((string) $attributeRaw);
                $operatorRaw = $condition['op'] ?? null;
                $operator = is_scalar($operatorRaw) ? trim((string) $operatorRaw) : '';
                $value = $condition['value'] ?? null;
                if ('in' === $operator && is_array($value)) {
                    $marks = [];
                    foreach ($value as $candidateValue) {
                        $key = ':p'.$parameterIndex++;
                        $marks[] = $key;
                        $params[$key] = $candidateValue;
                    }
                    $where[] = sprintf('%s IN (%s)', $attribute, implode(',', $marks));
                } elseif ('between' === $operator && is_array($value) && 2 === count($value)) {
                    $lowerBoundKey = ':p'.$parameterIndex++;
                    $upperBoundKey = ':p'.$parameterIndex++;
                    $where[] = sprintf('%s BETWEEN %s AND %s', $attribute, $lowerBoundKey, $upperBoundKey);
                    $params[$lowerBoundKey] = $value[0];
                    $params[$upperBoundKey] = $value[1];
                } elseif (in_array($operator, ['>', '>=', '<', '<='], true)) {
                    $comparisonKey = ':p'.$parameterIndex++;
                    $where[] = sprintf('%s %s %s', $attribute, $operator, $comparisonKey);
                    $params[$comparisonKey] = $value;
                } else {
                    throw new \InvalidArgumentException('Unsupported operator: '.$operator);
                }
                continue;
            }
            $tagRaw = $condition['tag'] ?? null;
            if (is_scalar($tagRaw)) {
                $tagKey = ':p'.$parameterIndex++;
                $where[] = 'JSON_CONTAINS(tag_set, '.$tagKey.')';
                $params[$tagKey] = json_encode((string) $tagRaw, JSON_THROW_ON_ERROR);
            }
        }

        return ['sql' => [] !== $where ? '('.implode(' AND ', $where).')' : '1=1', 'params' => $params];
    }
}
