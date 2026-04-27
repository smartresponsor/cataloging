<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Controller\Catalog;

use App\Cataloging\ServiceInterface\CategoryMoveInterface;
use App\Cataloging\ValueObject\CatalogMoveRequest;

/**
 * Handles the category move controller application flow.
 */
final readonly class CatalogCategoryMoveController
{
    /**
     * Initializes the category move controller service collaborators.
     */
    public function __construct(private CategoryMoveInterface $service)
    {
    }

    /**
     * @param array{nodeId:string,newParentId:string,treeId:string,policy:string,dryRun?:bool,locale?:string|null} $body
     *
     * @return array{ok:bool,changedCount:int,warnings:array<int,mixed>,redirects:array<int,mixed>}
     */
    public function move(array $body): array
    {
        [$count, $redirects] = $this->service->move(new CatalogMoveRequest(
            $body['nodeId'],
            $body['newParentId'],
            $body['treeId'],
            $body['policy'],
            (bool) ($body['dryRun'] ?? false),
            $body['locale'] ?? null,
        ));

        return [
            'ok' => true,
            'changedCount' => $count,
            'warnings' => [],
            'redirects' => $redirects,
        ];
    }
}
