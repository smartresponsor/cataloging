<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 */

namespace App\Controller\Admin;

use App\Service\CatalogCategoryMoveInterface as CategoryMoveInterface;

final class CategoryMoveController
{
    public function __construct(private readonly CategoryMoveInterface $service)
    {
    }

    /**
     * @param array{nodeId:string,newParentId:string,treeId:string,policy:string,dryRun?:bool,locale?:string|null} $body
     *
     * @return array{ok:bool,changedCount:int,warnings:array<int,mixed>,redirects:array<int,mixed>}
     */
    public function move(array $body): array
    {
        [$count, $redirects] = $this->service->move(
            $body['nodeId'],
            $body['newParentId'],
            $body['treeId'],
            $body['policy'],
            (bool) ($body['dryRun'] ?? false),
            $body['locale'] ?? null,
        );

        return [
            'ok' => true,
            'changedCount' => $count,
            'warnings' => [],
            'redirects' => $redirects,
        ];
    }
}
