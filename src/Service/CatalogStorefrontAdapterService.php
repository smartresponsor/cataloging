<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ValueObject\CategoryStorefrontAdaptRequest;

/**
 * Provides the storefront adapter application service.
 */
final class CatalogStorefrontAdapterService
{
    /**
     * @return list<array{id:mixed,name:string,slug:string,locale:string}>
     */
    public function adapt(CategoryStorefrontAdaptRequest $request): array
    {
        $adaptedTree = [];
        foreach ($request->tree() as $node) {
            if (!($node['published'] ?? true)) {
                continue;
            }
            $adaptedTree[] = [
                'id' => $node['id'],
                'nameEntity' => $this->stringValue($node, 'nameEntity'),
                'slug' => $this->stringValue($node, 'slug'),
                'locale' => $this->stringValue($node, 'locale', 'en'),
            ];
        }

        return $adaptedTree;
    }

    /** @param array<string,mixed> $node */
    private function stringValue(array $node, string $key, string $default = ''): string
    {
        $value = $node[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }
}
