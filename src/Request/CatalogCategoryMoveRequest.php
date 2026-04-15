<?php

declare(strict_types=1);

namespace App\Request;

use App\Request\Support\RequestValueNormalizer;
use App\ValueObject\CatalogCategoryMutationPolicy;

/**
 * Canonical request DTO for moving a catalog category.
 */
final readonly class CatalogCategoryMoveRequest
{
    /** @param list<string> $errors */
    public function __construct(
        public ?string $parentId,
        public string $treeId = 'catalog',
        public string $policy = 'strict',
        public bool $dryRun = false,
        public ?string $locale = null,
        private array $errors = [],
    ) {
    }

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        $errors = [];
        $parentId = null;
        $rawParentId = $data['parent_id'] ?? null;
        if (is_scalar($rawParentId)) {
            $parentId = trim((string) $rawParentId);
        }
        if (null === $parentId || '' === $parentId) {
            $errors[] = 'parent_id is required';
            $parentId = null;
        }

        $treeId = RequestValueNormalizer::trimmedStringOrDefault($data['tree_id'] ?? null, 'catalog');
        $policy = RequestValueNormalizer::trimmedStringOrDefault($data['policy'] ?? null, 'strict');
        try {
            $policy = CatalogCategoryMutationPolicy::fromString($policy)->value;
        } catch (\InvalidArgumentException) {
            $errors[] = 'policy must be one of: strict';
        }

        $dryRun = RequestValueNormalizer::boolFromMixed($data['dry_run'] ?? null, false);
        $locale = RequestValueNormalizer::optionalTrimmedString($data['locale'] ?? null);

        return new self($parentId, $treeId, $policy, $dryRun, $locale, $errors);
    }

    public function isValid(): bool
    {
        return [] === $this->errors;
    }

    /** @return list<string> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
