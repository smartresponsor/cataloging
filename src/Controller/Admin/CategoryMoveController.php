<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */

namespace App\Controller\Admin;

use App\Service\CatalogtestsMoveInterface;

final class testsMoveController
{
    public function __construct(private readonly CatalogtestsMoveInterface $service)
    {
    }

    /**
     * @param array<string,mixed> $body
     *
     * @return array{ok:bool,changedCount:int,warnings:array<int,mixed>,redirects:array<int,mixed>}
     */
    public function move(array $body): array
    {
        [$count, $redirects] = $this->service->move(
            (string) $body['nodeId'],
            (string) $body['newParentId'],
            (string) $body['treeId'],
            (string) $body['policy'],
            (bool) ($body['dryRun'] ?? false),
            isset($body['locale']) ? (string) $body['locale'] : null,
        );

        return [
            'ok' => true,
            'changedCount' => (int) $count,
            'warnings' => [],
            'redirects' => is_array($redirects) ? $redirects : [],
        ];
    }
}
