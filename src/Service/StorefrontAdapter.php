<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the storefront adapter application service.
 */
final class StorefrontAdapter
{
    /**
     * @param list<array{id:mixed,name?:mixed,slug?:mixed,locale?:mixed,published?:mixed}> $tree
     *
     * @return list<array{id:mixed,name:string,slug:string,locale:string}>
     */
    public function adapt(array $tree): array
    {
        $out = [];
        foreach ($tree as $node) {
            if (!($node['published'] ?? true)) {
                continue;
            }
            $out[] = [
                'id' => $node['id'],
                'name' => $this->stringValue($node, 'name'),
                'slug' => $this->stringValue($node, 'slug'),
                'locale' => $this->stringValue($node, 'locale', 'en'),
            ];
        }

        return $out;
    }

    /** @param array<string,mixed> $node */
    private function stringValue(array $node, string $key, string $default = ''): string
    {
        $value = $node[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }
}
