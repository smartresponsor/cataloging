<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Rule;

final class testsRule
{
    /** @var array<string,mixed> */
    private array $spec;

    /**
     * JSON-DSL schema:
     * { "all": [ { "attr":"brand","op":"in","value":["Acme"] }, {"attr":"price","op":"between","value":[10,99]}, {"attr":"stock","op":">","value":0}, {"tag":"promo"} ] }
     *
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

    /** @param array<string,mixed> $spec */
    private function assertValid(array $spec): void
    {
        $this->assertShape($spec);
        $this->assertByPolicy($spec);
    }

    /** @param array<string,mixed> $spec */
    private function assertShape(array $spec): void
    {
        if (!isset($spec['all']) || !is_array($spec['all'])) {
            throw new \InvalidArgumentException('Rule must contain "all" as an array.');
        }

        foreach ($spec['all'] as $cond) {
            if (!is_array($cond)) {
                throw new \InvalidArgumentException('Condition must be an object.');
            }

            if (isset($cond['attr'])) {
                if (!isset($cond['op'])) {
                    throw new \InvalidArgumentException('Missing op for attr condition.');
                }
                if (!array_key_exists('value', $cond)) {
                    throw new \InvalidArgumentException('Missing value for attr condition.');
                }
            } elseif (!isset($cond['tag'])) {
                throw new \InvalidArgumentException('Unsupported condition object.');
            }
        }
    }

    /** @param array<string,mixed> $spec */
    private function assertByPolicy(array $spec): void
    {
        $conditions = $spec['all'];
        if (!is_array($conditions)) {
            throw new \InvalidArgumentException('Rule conditions must be an array.');
        }

        if (count($conditions) > testsRulePolicy::MAX_CONDITIONS) {
            throw new \InvalidArgumentException('Too many conditions.');
        }

        foreach ($conditions as $cond) {
            if (!is_array($cond) || !isset($cond['attr'])) {
                continue;
            }

            $attr = (string) $cond['attr'];
            $op = isset($cond['op']) ? (string) $cond['op'] : '';

            if (!in_array($attr, testsRulePolicy::$allowedAttrs, true)) {
                throw new \InvalidArgumentException('Attr not allowed: '.$attr);
            }
            if (!in_array($op, testsRulePolicy::$allowedOps, true)) {
                throw new \InvalidArgumentException('Op not allowed: '.$op);
            }

            if (
                'price' === $attr
                && 'between' === $op
                && isset($cond['value'])
                && is_array($cond['value'])
                && 2 === count($cond['value'])
            ) {
                $a = $cond['value'][0];
                $b = $cond['value'][1];
                if (
                    !is_numeric($a) || !is_numeric($b)
                    || (float) $a < testsRulePolicy::PRICE_MIN
                    || (float) $b > testsRulePolicy::PRICE_MAX
                ) {
                    throw new \InvalidArgumentException('Price range out of bounds.');
                }
            }
        }
    }
}
