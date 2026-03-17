<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */

namespace App\Controller\Admin;

use App\Service\CatalogCategoryMoveInterface;
use App\Service\MovePolicy;

final class CategoryMoveController
{
    public function __construct(private readonly CatalogCategoryMoveInterface $service)
    {
    }

    public function __invoke(array $body): array
    {
        return $this->move($body);
    }

    public function move(array $body): array
    {
        $nodeId = trim((string) ($body['nodeId'] ?? ''));
        $newParentId = trim((string) ($body['newParentId'] ?? ''));
        $treeId = trim((string) ($body['treeId'] ?? ''));
        $policy = trim((string) ($body['policy'] ?? ''));

        if ('' === $nodeId) {
            throw new \InvalidArgumentException('nodeId is required');
        }
        if ('' === $newParentId) {
            throw new \InvalidArgumentException('newParentId is required');
        }
        if ('' === $treeId) {
            throw new \InvalidArgumentException('treeId is required');
        }
        if (!in_array($policy, [MovePolicy::PreserveSlug, MovePolicy::RebuildSlug], true)) {
            throw new \InvalidArgumentException('policy is invalid');
        }

        [$count, $redirects] = $this->service->move(
            $nodeId,
            $newParentId,
            $treeId,
            $policy,
            (bool) ($body['dryRun'] ?? false),
            isset($body['locale']) ? (string) $body['locale'] : null,
        );

        return [
            'ok' => true,
            'changedCount' => $count,
            'warnings' => [],
            'redirects' => $redirects,
        ];
    }
}
