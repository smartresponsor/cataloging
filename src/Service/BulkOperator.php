<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the bulk operator application service.
 */
final class BulkOperator
{
    /**
     * @param list<int|string> $items
     *
     * @return array{success:list<array{id:int,action:string}>,failed:list<array{id:int|string,reason:string}>}
     */
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
