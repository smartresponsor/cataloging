<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Audit;

/**
 * Provides the action logger implementation.
 */
final class ActionLogger
{
    /**
     * @param string              $actor
     * @param string              $action
     * @param array<string,mixed> $target
     * @param array<string,mixed> $ctx
     *
     * @throws \JsonException
     */
    public function log(string $actor, string $action, array $target, array $ctx = []): void
    {
        $row = [
            'actor' => $actor,
            'action' => $action,
            'target' => $target,
            'ctx' => $ctx,
            'ts' => date(DATE_ATOM),
        ];
        file_put_contents('report/category-audit-latest.json',
            json_encode(
                $row,
                JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT,
            ),
        );
    }
}
