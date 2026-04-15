<?php

declare(strict_types=1);

namespace App\Request;

use App\Request\Support\RequestValueNormalizer;

/**
 * Canonical request DTO for publishing/unpublishing a catalog category.
 */
final readonly class CatalogCategoryPublishRequest
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $errors
     */
    public function __construct(
        public ?bool $published,
        public array $checks = [],
        public string $reason = 'api publish request',
        private array $errors = [],
    ) {
    }

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        $errors = [];
        if (!array_key_exists('published', $data)) {
            return new self(null, [], 'api publish request', ['published is required']);
        }
        $published = RequestValueNormalizer::nullableBoolFromMixed($data['published']);
        if (null === $published) {
            $errors[] = 'published must be boolean';
        }

        $checks = [];
        if (is_array($data['checks'] ?? null)) {
            foreach ($data['checks'] as $name => $value) {
                if (!is_string($name)) {
                    continue;
                }
                $checks[$name] = (bool) $value;
            }
        }

        $reason = RequestValueNormalizer::trimmedStringOrDefault($data['reason'] ?? null, 'api publish request');

        if (true === $published && [] === $checks) {
            $errors[] = 'checks are required when published is true';
        }

        return new self($published, $checks, $reason, $errors);
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
