<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\RepositoryInterface\VirtualCategoryRepositoryInterface;
use Doctrine\DBAL\Connection;
/**
 * Provides repository services for virtual category repository.
 */
final class VirtualCategoryRepository implements VirtualCategoryRepositoryInterface
{
    /**
     * Initializes the virtual category repository service collaborators.
     */
    public function __construct(private readonly Connection $connection)
    {
    }
    /**
     * Handles the find by id workflow.
     */
    public function findById(string $id): ?array
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        try {
            $row = $this->connection->fetchAssociative(
                'SELECT id, name, rule FROM virtual_category WHERE id = :id LIMIT 1',
                ['id' => $normalizedId],
            );
        } catch (\Throwable) {
            return null;
        }

        if (!is_array($row)) {
            return null;
        }

        $rawRule = $row['rule'] ?? null;
        $decodedRule = is_string($rawRule) ? json_decode($rawRule, true) : $rawRule;
        $rule = is_array($decodedRule) ? $decodedRule : [];

        return [
            'id' => is_scalar($row['id'] ?? null) ? (string) $row['id'] : '',
            'name' => is_scalar($row['name'] ?? null) ? (string) $row['name'] : '',
            'rule' => $rule,
        ];
    }
}
