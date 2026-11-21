<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries GraphQL facet lookup input for category read adapters.
 */
final readonly class CategoryGraphqlFacetRequest
{
    /**
     * Initializes the category GraphQL facet request value object.
     */
    public function __construct(
        private string $term,
        private string $locale = 'en',
        private ?string $tenant = null,
        private ?string $pathPrefix = null,
        private int $limit = 20,
        private int $offset = 0,
    ) {
    }

    /**
     * @param array<string,mixed> $args
     */
    public static function fromArray(array $args): self
    {
        $term = $args['term'] ?? '';
        $locale = $args['locale'] ?? 'en';
        $tenant = $args['tenant'] ?? null;
        $pathPrefix = $args['pathPrefix'] ?? null;
        $limit = $args['limit'] ?? 20;
        $offset = $args['offset'] ?? 0;

        return new self(
            is_scalar($term) ? trim((string) $term) : '',
            is_scalar($locale) ? trim((string) $locale) : 'en',
            is_scalar($tenant) ? trim((string) $tenant) : null,
            is_scalar($pathPrefix) ? trim((string) $pathPrefix) : null,
            is_numeric($limit) ? (int) $limit : 20,
            is_numeric($offset) ? (int) $offset : 0,
        );
    }

    public function term(): string
    {
        return $this->term;
    }

    public function locale(): string
    {
        return '' === $this->locale ? 'en' : $this->locale;
    }

    public function tenant(): ?string
    {
        return $this->tenant;
    }

    public function pathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return $this->offset;
    }
}
