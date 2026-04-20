<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\RepositoryInterface\CatalogCollectionProjectionRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Provides repository services for catalog collection projection repository.
 */
final readonly class CatalogCollectionProjectionRepository implements CatalogCollectionProjectionRepositoryInterface
{
    /**
     * Initializes the catalog collection projection repository service collaborators.
     */
    public function __construct(private ManagerRegistry $registry)
    {
    }

    /**
     * Handles the list workflow.
     */
    public function list(): array
    {
        try {
            /** @var Connection $connection */
            $connection = $this->registry->getConnection('infra');
            $rows = $connection->fetchAllAssociative(
                'SELECT id, brand, price, stock, tag_set FROM record_index ORDER BY id ASC'
            );
        } catch (\Throwable) {
            return [];
        }

        return array_values(array_filter(array_map(
            function (array $row): ?array {
                $id = $row['id'] ?? null;
                if (!is_scalar($id)) {
                    return null;
                }

                $item = [
                    'id' => (string) $id,
                    'brand' => is_scalar($row['brand'] ?? null) ? (string) $row['brand'] : null,
                    'price' => is_numeric($row['price'] ?? null) ? (float) $row['price'] : null,
                    'stock' => is_numeric($row['stock'] ?? null) ? (int) $row['stock'] : null,
                ];

                $rawTagSet = $row['tag_set'] ?? null;
                if (is_string($rawTagSet) && '' !== trim($rawTagSet)) {
                    $decoded = json_decode($rawTagSet, true);
                    if (is_array($decoded)) {
                        $tags = [];
                        foreach ($decoded as $value) {
                            if (is_bool($value) || is_float($value) || is_int($value) || is_string($value)) {
                                $tags[] = $value;
                            }
                        }
                        if ([] !== $tags) {
                            $item['tag_set'] = $tags;
                        }
                    }
                }

                return $item;
            },
            $rows,
        )));
    }
}
