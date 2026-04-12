<?php

declare(strict_types=1);

namespace App\Service;

use App\ValueObject\CategoryProjectionValueNormalizer;
use Doctrine\DBAL\ParameterType;

/**
 * Provides shared category projection query normalization and filter support.
 */
/** @noinspection DuplicatedCode */
final class CategoryProjectionQuerySupport
{
    /**
     * @param array<string,mixed> $criteria
     *
     * @return array{
     *     tenant: ?string,
     *     locale: ?string,
     *     workflow_state: ?string,
     *     published: ?bool,
     * }
     */
    public function normalizeProjectionCriteriaMap(array $criteria): array
    {
        return [
            'tenant' => $this->optionalString($criteria['tenant'] ?? null),
            'locale' => $this->optionalString($criteria['locale'] ?? null),
            'workflow_state' => $this->optionalString($criteria['workflow_state'] ?? null),
            'published' => $this->optionalBool($criteria['published'] ?? null),
        ];
    }

    /**
     * @param array<string,mixed> $criteria
     *
     * @return array{0:string,1:array<string,mixed>,2:array<string, ParameterType>}
     */
    public function compileProjectionFilters(array $criteria): array
    {
        $clauses = [];
        /** @var array<string,mixed> $params */
        $params = [];
        /** @var array<string,ParameterType> $types */
        $types = [];

        if (null !== $criteria['tenant']) {
            $clauses[] = 'tenant = :tenant';
            $params['tenant'] = $criteria['tenant'];
            $types['tenant'] = ParameterType::STRING;
        }

        if (null !== $criteria['locale']) {
            $clauses[] = 'locale = :locale';
            $params['locale'] = $criteria['locale'];
            $types['locale'] = ParameterType::STRING;
        }

        if (null !== $criteria['workflow_state']) {
            $clauses[] = 'workflow_state = :workflowState';
            $params['workflowState'] = $criteria['workflow_state'];
            $types['workflowState'] = ParameterType::STRING;
        }

        if (null !== $criteria['published']) {
            $clauses[] = 'published = :published';
            $params['published'] = $criteria['published'];
            $types['published'] = ParameterType::BOOLEAN;
        }

        if ([] === $clauses) {
            return ['', [], []];
        }

        return [' WHERE '.implode(' AND ', $clauses), $params, $types];
    }

    /**
     * @param list<array<string,mixed>> $rows
     *
     * @return list<array{
     *     id:string,
     *     slug:string,
     *     name:string,
     *     parent_id: ?string,
     *     path:string,
     *     locale:string,
     *     tenant:string,
     *     workflow_state:string,
     *     published:bool,
     *     published_at: ?string,
     *     updated_at:string,
     * }>
     */
    public function normalizeProjectionRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $normalized = $this->normalizeProjectionRow($row);
            if (null !== $normalized) {
                $result[] = $normalized;
            }
        }

        return $result;
    }

    public function optionalString(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return '' === $normalized ? null : $normalized;
    }

    public function optionalBool(mixed $value): ?bool
    {
        return CategoryProjectionValueNormalizer::boolValue($value);
    }

    /**
     * @param array<string,mixed> $row
     *
     * @return array{
     *     id:string,
     *     slug:string,
     *     name:string,
     *     parent_id: ?string,
     *     path:string,
     *     locale:string,
     *     tenant:string,
     *     workflow_state:string,
     *     published:bool,
     *     published_at: ?string,
     *     updated_at:string,
     * }|null
     */
    private function normalizeProjectionRow(array $row): ?array
    {
        $id = $this->optionalString($row['id'] ?? null);
        if (null === $id) {
            return null;
        }

        return [
            'id' => $id,
            'slug' => $this->optionalString($row['slug'] ?? null) ?? '',
            'name' => $this->optionalString($row['name'] ?? null) ?? '',
            'parent_id' => $this->optionalString($row['parent_id'] ?? null),
            'path' => $this->optionalString($row['path'] ?? null) ?? '',
            'locale' => $this->optionalString($row['locale'] ?? null) ?? '',
            'tenant' => $this->optionalString($row['tenant'] ?? null) ?? 'default',
            'workflow_state' => $this->optionalString($row['workflow_state'] ?? null) ?? 'draft',
            'published' => $this->optionalBool($row['published'] ?? false) ?? false,
            'published_at' => $this->optionalString($row['published_at'] ?? null),
            'updated_at' => $this->optionalString($row['updated_at'] ?? null) ?? '',
        ];
    }
}
