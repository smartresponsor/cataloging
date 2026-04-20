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
