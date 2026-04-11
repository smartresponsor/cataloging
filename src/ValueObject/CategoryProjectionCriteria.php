<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries projection/search/list criteria across read-oriented category surfaces.
 */
final readonly class CategoryProjectionCriteria
{
    public function __construct(
        private string $q = '',
        private ?string $tenant = null,
        private ?string $locale = null,
        private ?string $workflowState = null,
        private ?bool $published = null,
        private ?int $limit = null,
        private ?int $offset = null,
        private ?string $order = null,
        private ?string $direction = null,
    ) {
    }

    /** @param array<string,mixed> $input */
    public static function fromArray(array $input): self
    {
        return new self(
            self::stringValue($input['q'] ?? null) ?? '',
            self::stringValue($input['tenant'] ?? null),
            self::stringValue($input['locale'] ?? null),
            self::stringValue($input['workflow_state'] ?? null),
            self::boolValue($input['published'] ?? null),
            self::intValue($input['limit'] ?? null),
            self::intValue($input['offset'] ?? null),
            self::stringValue($input['order'] ?? null),
            self::stringValue($input['direction'] ?? null),
        );
    }

    public function tenant(): ?string
    {
        return $this->tenant;
    }

    public function published(): ?bool
    {
        return $this->published;
    }

    public function withTenant(?string $tenant): self
    {
        return new self(
            $this->q,
            $tenant,
            $this->locale,
            $this->workflowState,
            $this->published,
            $this->limit,
            $this->offset,
            $this->order,
            $this->direction,
        );
    }

    public function withPublished(?bool $published): self
    {
        return new self(
            $this->q,
            $this->tenant,
            $this->locale,
            $this->workflowState,
            $published,
            $this->limit,
            $this->offset,
            $this->order,
            $this->direction,
        );
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'q' => $this->q,
            'tenant' => $this->tenant,
            'locale' => $this->locale,
            'workflow_state' => $this->workflowState,
            'published' => $this->published,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'order' => $this->order,
            'direction' => $this->direction,
        ];
    }

    private static function stringValue(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return '' === $normalized ? null : $normalized;
    }

    private static function boolValue(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (!is_scalar($value)) {
            return null;
        }

        $normalized = strtolower(trim((string) $value));
        if (in_array($normalized, ['1', 'true', 'yes'], true)) {
            return true;
        }

        if (in_array($normalized, ['0', 'false', 'no'], true)) {
            return false;
        }

        return null;
    }

    private static function intValue(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
