<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Category\Rule;

use App\Cataloging\ServiceInterface\Category\CategoryRuleEngineInterface;

/**
 * Provides the category rule engine application service.
 */
final class CategoryRuleEngine implements CategoryRuleEngineInterface
{
    /**
     * @param array<string,mixed> $rule
     * @param array<string,mixed> $payload
     */
    public function match(array $rule, array $payload): bool
    {
        return $this->evalNode($this->map($rule['condition'] ?? null), $payload);
    }

    /**
     * @param array<string,mixed> $node
     * @param array<string,mixed> $payload
     */
    private function evalNode(array $node, array $payload): bool
    {
        if (array_any($this->nodeList($node, 'all'), fn (array $child): bool => !$this->evalNode($child, $payload))) {
            return false;
        }
        if (isset($node['all'])) {
            return true;
        }

        if (array_any($this->nodeList($node, 'any'), fn (array $child): bool => $this->evalNode($child, $payload))) {
            return true;
        }
        if (isset($node['any'])) {
            return false;
        }

        if (array_any($this->nodeList($node, 'none'), fn (array $child): bool => $this->evalNode($child, $payload))) {
            return false;
        }
        if (isset($node['none'])) {
            return true;
        }

        $attr = $node['attr'] ?? null;
        $operator = $node['op'] ?? null;
        $value = $node['value'] ?? null;
        if (!is_string($attr) || !is_string($operator)) {
            return false;
        }
        $payloadValue = $payload[$attr] ?? null;

        return match ($operator) {
            'eq' => $payloadValue === $value,
            'neq' => $payloadValue !== $value,
            'lt' => is_numeric($payloadValue) && is_numeric($value) && (float) $payloadValue < (float) $value,
            'lte' => is_numeric($payloadValue) && is_numeric($value) && (float) $payloadValue <= (float) $value,
            'gt' => is_numeric($payloadValue) && is_numeric($value) && (float) $payloadValue > (float) $value,
            'gte' => is_numeric($payloadValue) && is_numeric($value) && (float) $payloadValue >= (float) $value,
            'in' => is_array($value) && in_array($payloadValue, $value, true),
            'inTree' => is_string($value) && is_string($payloadValue) && str_starts_with($payloadValue, $value),
            default => false,
        };
    }

    /**
     * @param array<string,mixed> $node
     *
     * @return list<array<string,mixed>>
     */
    private function nodeList(array $node, string $key): array
    {
        $value = $node[$key] ?? [];
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $child) {
            $childNode = $this->map($child);
            if ([] !== $childNode) {
                $normalized[] = $childNode;
            }
        }

        return $normalized;
    }

    /** @return array<string,mixed> */
    private function map(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                continue;
            }
            $normalized[$key] = $item;
        }

        return $normalized;
    }
}
