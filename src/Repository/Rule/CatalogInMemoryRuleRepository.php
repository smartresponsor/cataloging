<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Cataloging\Repository\Rule;

use App\Cataloging\ServiceInterface\Rule\RuleRepositoryInterface;

final class CatalogInMemoryRuleRepository implements RuleRepositoryInterface
{
    /** @var array<string, array<string, mixed>> */
    private array $ruleMap = [];

    /** @param array<string, mixed> $rule */
    public function save(array $rule): string
    {
        $id = $this->id($rule);
        $this->ruleMap[$id] = array_replace($this->ruleMap[$id] ?? [], $rule, ['id' => $id]);

        return $id;
    }

    /** @return array<string, mixed>|null */
    public function find(string $id): ?array
    {
        return $this->ruleMap[$id] ?? null;
    }

    /**
     * @param array<string, mixed> $opt
     *
     * @return list<array<string, mixed>>
     */
    public function list(array $opt = []): array
    {
        if ([] === $opt) {
            return array_values($this->ruleMap);
        }

        return array_values(array_filter(
            $this->ruleMap,
            static fn (array $rule): bool => self::matches($rule, $opt),
        ));
    }

    /** @param array<string, mixed> $rule */
    private function id(array $rule): string
    {
        $id = $rule['id'] ?? null;

        return is_scalar($id) && '' !== trim((string) $id)
            ? trim((string) $id)
            : sha1(json_encode($rule, JSON_UNESCAPED_SLASHES) ?: serialize($rule));
    }

    /**
     * @param array<string, mixed> $rule
     * @param array<string, mixed> $filter
     */
    private static function matches(array $rule, array $filter): bool
    {
        foreach ($filter as $key => $value) {
            if (null === $value || '' === $value) {
                continue;
            }

            if ((string) ($rule[$key] ?? '') !== (string) $value) {
                return false;
            }
        }

        return true;
    }
}
