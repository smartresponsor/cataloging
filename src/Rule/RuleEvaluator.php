<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Rule;

final class RuleEvaluator
{
    /**
     * Compile rule to SQL WHERE fragment and parameters.
     *
     * @return array{sql:string,params:array<string,mixed>}
     */
    public function compile(CategoryRule $rule): array
    {
        $where = [];
        $params = [];
        $index = 0;

        foreach ($rule->spec()['all'] as $condition) {
            if (isset($condition['attr'])) {
                $this->compileAttributeCondition($condition, $where, $params, $index);
                continue;
            }

            if (isset($condition['tag'])) {
                $parameter = ':p'.$index++;
                $where[] = 'JSON_CONTAINS(tag_set, '.$parameter.')';
                $params[$parameter] = json_encode($condition['tag'], JSON_THROW_ON_ERROR);
            }
        }

        return [
            'sql' => [] !== $where ? '('.implode(' AND ', $where).')' : '1=1',
            'params' => $params,
        ];
    }

    /**
     * @param array<string,mixed> $condition
     * @param list<string>        $where
     * @param array<string,mixed> $params
     */
    private function compileAttributeCondition(array $condition, array &$where, array &$params, int &$index): void
    {
        $attribute = (string) $condition['attr'];
        $operator = (string) $condition['op'];
        $value = $condition['value'];

        if ('in' === $operator && is_array($value)) {
            $marks = [];
            foreach ($value as $item) {
                $parameter = ':p'.$index++;
                $marks[] = $parameter;
                $params[$parameter] = $item;
            }
            $where[] = sprintf('%s IN (%s)', $attribute, implode(',', $marks));

            return;
        }

        if ('between' === $operator && is_array($value) && 2 === count($value)) {
            $left = ':p'.$index++;
            $right = ':p'.$index++;
            $where[] = sprintf('%s BETWEEN %s AND %s', $attribute, $left, $right);
            $params[$left] = array_values($value)[0];
            $params[$right] = array_values($value)[1];

            return;
        }

        if (in_array($operator, ['>', '>=', '<', '<='], true)) {
            $parameter = ':p'.$index++;
            $where[] = sprintf('%s %s %s', $attribute, $operator, $parameter);
            $params[$parameter] = $value;

            return;
        }

        throw new \InvalidArgumentException('Unsupported operator: '.$operator);
    }
}
