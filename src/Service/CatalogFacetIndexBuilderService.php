<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the facet index builder application service.
 */
final class CatalogFacetIndexBuilderService
{
    /**
     * @param array{id?:mixed,slug?:mixed,path?:mixed,locale?:mixed,nameEntity?:mixed} $category
     *
     * @return array{id:string,slug:string,path:string,locale:string,name:string}
     */
    public function build(array $category): array
    {
        return [
            'id' => $this->stringValue($category, 'id'),
            'slug' => $this->stringValue($category, 'slug'),
            'path' => $this->stringValue($category, 'path'),
            'locale' => $this->stringValue($category, 'locale', 'en'),
            'name' => $this->stringValue($category, 'name', $this->stringValue($category, 'nameEntity')),
        ];
    }

    /**
     * Builds a stable counted-facet contract for storefront/search consumers.
     *
     * @param array<string, int> $buckets
     *
     * @return array{identifier:string,buckets:array<string,int>}
     */
    public function buildCountContract(string $identifier, array $buckets): array
    {
        $facetIdentifier = mb_strtolower(trim($identifier));
        if ('' === $facetIdentifier || !preg_match('/^[a-z0-9_\\-]+$/', $facetIdentifier)) {
            throw new \InvalidArgumentException('Facet identifier must use lowercase letters, numbers, dash, or underscore.');
        }

        $normalizedBuckets = [];
        foreach ($buckets as $valueIdentifier => $count) {
            $normalizedIdentifier = mb_strtolower(trim((string) $valueIdentifier));
            if (
                '' === $normalizedIdentifier
                || mb_strlen($normalizedIdentifier) > 128
                || !preg_match('/^[a-z0-9][a-z0-9_.:\\-]*$/', $normalizedIdentifier)
            ) {
                throw new \InvalidArgumentException('Facet value identifier uses an unsupported format.');
            }

            if ($count < 0) {
                throw new \InvalidArgumentException('Facet bucket count must be non-negative.');
            }

            $normalizedBuckets[$normalizedIdentifier] = $count;
        }

        uksort($normalizedBuckets, static function (string $left, string $right) use ($normalizedBuckets): int {
            $countComparison = $normalizedBuckets[$right] <=> $normalizedBuckets[$left];

            return 0 !== $countComparison ? $countComparison : strcmp($left, $right);
        });

        return [
            'identifier' => $facetIdentifier,
            'buckets' => $normalizedBuckets,
        ];
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? (string) $value : $default;
    }
}
