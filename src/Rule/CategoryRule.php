<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Rule;

final class CategoryRule
{
    /** @var array<string,mixed> */
    private array $spec;

    /**
     * @param array<string,mixed> $spec
     */
    public function __construct(array $spec)
    {
        $this->assertValid($spec);
        $this->spec = $spec;
    }

    /** @return array<string,mixed> */
    public function spec(): array
    {
        return $this->spec;
    }

    /**
     * @param array<string,mixed> $spec
     */
    private function assertValid(array $spec): void
    {
        if (!isset($spec['all']) || !is_array($spec['all'])) {
            throw new \InvalidArgumentException('Rule must contain "all" as an array.');
        }

        if (count($spec['all']) > CategoryRulePolicy::MAX_CONDITIONS) {
            throw new \InvalidArgumentException('Too many conditions.');
        }

        foreach ($spec['all'] as $condition) {
            if (!is_array($condition)) {
                throw new \InvalidArgumentException('Condition must be an object.');
            }

            if (isset($condition['attr'])) {
                $this->assertAttributeCondition($condition);
                continue;
            }

            if (isset($condition['tag'])) {
                continue;
            }

            throw new \InvalidArgumentException('Unsupported condition object.');
        }
    }

    /**
     * @param array<string,mixed> $condition
     */
    private function assertAttributeCondition(array $condition): void
    {
        if (!isset($condition['op'])) {
            throw new \InvalidArgumentException('Missing op for attr condition.');
        }
        if (!array_key_exists('value', $condition)) {
            throw new \InvalidArgumentException('Missing value for attr condition.');
        }
        if (!in_array($condition['attr'], CategoryRulePolicy::$allowedAttrs, true)) {
            throw new \InvalidArgumentException('Attr not allowed: '.$condition['attr']);
        }
        if (!in_array($condition['op'], CategoryRulePolicy::$allowedOps, true)) {
            throw new \InvalidArgumentException('Op not allowed: '.$condition['op']);
        }

        $value = $condition['value'];
        if (
            'price' === $condition['attr']
            && 'between' === $condition['op']
            && is_array($value)
            && 2 === count($value)
        ) {
            [$min, $max] = array_values($value);
            if ($min < CategoryRulePolicy::PRICE_MIN || $max > CategoryRulePolicy::PRICE_MAX) {
                throw new \InvalidArgumentException('Price range out of bounds.');
            }
        }
    }
}
