<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ServiceInterface\GraphqlResolverInterface;
use App\ValueObject\CategoryGraphqlMoveRequest;
use App\ValueObject\CategoryGraphqlNodeRequest;
use App\ValueObject\CategoryGraphqlPublishRequest;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Secondary GraphQL read adapter over the canonical projection-backed catalog read model.
 *
 * This service is intentionally not the primary domain boundary. It exists as a
 * compatibility/convenience read surface and should stay thin.
 */
final readonly class GraphqlResolver implements GraphqlResolverInterface
{
    /**
     * Initializes the graphql resolver service collaborators.
     */
    public function __construct(
        private CategoryProjectionReadServiceInterface $categoryProjectionReadService,
        private ManagerRegistry $registry,
        private ?PublishOperation $publish = null,
        private ?TreeOperation $tree = null,
    ) {
    }

    /** @return array<string,mixed>|null */
    public function category(CategoryGraphqlNodeRequest $request): ?array
    {
        $id = $request->id();
        if ('' === $id) {
            return null;
        }

        $row = $this->categoryProjectionReadService->findOne($id);
        if (null === $row) {
            return null;
        }

        return $this->normalizeNode($row);
    }

    /**
     * @return list<array<string,mixed>>
     *
     * @throws Exception
     */
    public function categoryPath(CategoryGraphqlNodeRequest $request): array
    {
        $id = $request->id();
        if ('' === $id) {
            return [];
        }

        $row = $this->categoryProjectionReadService->findOne($id);
        if (null === $row) {
            return [];
        }

        $path = $this->stringValue($row, 'path');
        if ('' === $path) {
            return [$this->normalizeNode($row)];
        }

        $prefixes = $this->pathPrefixes($path);
        if ([] === $prefixes) {
            return [$this->normalizeNode($row)];
        }

        $placeholders = [];
        $params = [];
        $types = [];
        foreach ($prefixes as $index => $prefix) {
            $key = 'path'.$index;
            $placeholders[] = ':'.$key;
            $params[$key] = $prefix;
            $types[$key] = ParameterType::STRING;
        }

        $rows = $this->infraConnection()->fetchAllAssociative(
            'SELECT id, parent_id, slug, name, locale, workflow_state, published, path '
            .'FROM category_projection WHERE path IN ('.implode(', ', $placeholders).') '
            .'ORDER BY LENGTH(path) ASC, path ASC',
            $params,
            $types,
        );

        $result = [];
        foreach ($rows as $pathRow) {
            $result[] = $this->normalizeNode($pathRow);
        }

        return [] === $result ? [$this->normalizeNode($row)] : $result;
    }

    /** @return array<string,mixed>|null */
    public function publishCategory(CategoryGraphqlPublishRequest $request): ?array
    {
        $id = $request->id();
        if ('' === $id || null === $this->publish) {
            return null;
        }

        $status = new Status(Status::DRAFT);
        $published = $this->publish->publish($status);

        return [
            'id' => $id,
            'status' => $published->value(),
        ];
    }

    public function moveCategory(CategoryGraphqlMoveRequest $request): bool
    {
        $id = $request->id();
        if ('' === $id || null === $this->tree) {
            return false;
        }

        $this->tree->move($id, $request->parentId());

        return true;
    }

    private function infraConnection(): Connection
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('infra');

        return $connection;
    }

    /**
     * @param array<string,mixed> $row
     *
     * @return array<string,mixed>
     */
    private function normalizeNode(array $row): array
    {
        $published = $this->boolValue($row['published'] ?? false);
        $workflowState = $this->stringValue($row, 'workflow_state', $published ? Status::PUBLISHED : Status::DRAFT);

        return [
            'id' => $this->stringValue($row, 'id'),
            'parentId' => $this->nullableStringValue($row, 'parent_id'),
            'slug' => $this->stringValue($row, 'slug'),
            'name' => $this->stringValue($row, 'name'),
            'locale' => $this->stringValue($row, 'locale', 'en'),
            'status' => $published ? Status::PUBLISHED : $workflowState,
        ];
    }

    /** @return list<string> */
    private function pathPrefixes(string $path): array
    {
        $segments = array_values(array_filter(
            explode('.', trim($path)),
            static fn (string $segment): bool => '' !== trim($segment),
        ));
        $prefixes = [];
        $current = [];
        foreach ($segments as $segment) {
            $current[] = $segment;
            $prefixes[] = implode('.', $current);
        }

        return $prefixes;
    }

    /** @param array<string,mixed> $input */
    private function stringValue(array $input, string $key, string $default = ''): string
    {
        $value = $input[$key] ?? $default;

        return is_scalar($value) ? trim((string) $value) : $default;
    }

    /** @param array<string,mixed> $input */
    private function nullableStringValue(array $input, string $key): ?string
    {
        if (!array_key_exists($key, $input) || null === $input[$key]) {
            return null;
        }
        $value = $input[$key];

        return is_scalar($value) ? trim((string) $value) : null;
    }

    private function boolValue(mixed $value): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_int($value) => 0 !== $value,
            is_string($value) => in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true),
            default => false,
        };
    }
}
