<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Audit\Category;

final class ActionLogger
{
    public function log(string $actor, string $action, array $target, array $ctx = []): void
    {
        $row = [
            'actor' => $actor,
            'action' => $action,
            'target' => $target,
            'ctx' => $ctx,
            'ts' => date(DATE_ATOM),
        ];
        file_put_contents('report/category-audit-latest.json', json_encode($row, JSON_PRETTY_PRINT));
    }
}
