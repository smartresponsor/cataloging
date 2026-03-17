<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\Repository\CategoryRepository;

final class BulkOperator
{
    public function __construct(private readonly ?CategoryRepository $repository = null)
    {
    }

    public function run(array $items, string $action): array
    {
        if (null !== $this->repository && in_array($action, ['publish', 'unpublish'], true)) {
            return $this->repository->bulkSetPublished($items, 'publish' === $action, 'bulk-operator');
        }

        $result = ['success' => [], 'failed' => []];
        foreach ($items as $id) {
            if (!is_numeric($id)) {
                $result['failed'][] = ['id' => $id, 'reason' => 'invalid'];
                continue;
            }
            $result['success'][] = ['id' => (int) $id, 'action' => $action];
        }

        return $result;
    }
}
