/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */
<?php
declare(strict_types=1);

namespace App\Http\Controller\Admin;

use App\Service\CategoryMoveInterface;

final class CategoryMoveController
{
    private CategoryMoveInterface $service;

    public function __construct(CategoryMoveInterface $service)
    {
        $this->service = $service;
    }

    // Pseudo-code: integrate with your framework routing (Symfony/Laravel/etc.)
    public function move(array $body): array
    {
        [$count, $redirects] = $this->service->move(
            $body['nodeId'], $body['newParentId'], $body['treeId'], $body['policy'], (bool) ($body['dryRun'] ?? false), $body['locale'] ?? null
        );

        return [
            'ok' => true,
            'changedCount' => $count,
            'warnings' => [],
            'redirects' => $redirects,
        ];
    }
}
