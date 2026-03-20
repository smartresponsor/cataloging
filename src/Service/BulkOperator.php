<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class BulkOperator
{
    public function run(array $items, string $action): array
    {
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
