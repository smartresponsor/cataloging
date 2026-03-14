<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Command\Category;

class RuleEngine
{
    public function match(array $rule, array $payload): bool
    {
        return $this->evalNode($rule['condition'] ?? [], $payload);
    }

    private function evalNode(array $node, array $payload): bool
    {
        if (isset($node['all']) && is_array($node['all'])) {
            foreach ($node['all'] as $child) {
                if (!is_array($child) || !$this->evalNode($child, $payload)) {
                    return false;
                }
            }

            return true;
        }

        if (isset($node['any']) && is_array($node['any'])) {
            foreach ($node['any'] as $child) {
                if (is_array($child) && $this->evalNode($child, $payload)) {
                    return true;
                }
            }

            return false;
        }

        if (isset($node['none']) && is_array($node['none'])) {
            foreach ($node['none'] as $child) {
                if (is_array($child) && $this->evalNode($child, $payload)) {
                    return false;
                }
            }

            return true;
        }

        $attribute = $node['attr'] ?? null;
        $operator = $node['op'] ?? null;
        $value = $node['value'] ?? null;
        if (!is_string($attribute) || !is_string($operator)) {
            return false;
        }

        $actual = $payload[$attribute] ?? null;

        return match ($operator) {
            'eq' => $actual === $value,
            'neq' => $actual !== $value,
            'lt' => is_numeric($actual) && is_numeric($value) && (float) $actual < (float) $value,
            'lte' => is_numeric($actual) && is_numeric($value) && (float) $actual <= (float) $value,
            'gt' => is_numeric($actual) && is_numeric($value) && (float) $actual > (float) $value,
            'gte' => is_numeric($actual) && is_numeric($value) && (float) $actual >= (float) $value,
            'in' => is_array($value) && in_array($actual, $value, true),
            'inTree' => is_string($value) && is_string((string) $actual) && str_starts_with((string) $actual, $value),
            default => false,
        };
    }
}
