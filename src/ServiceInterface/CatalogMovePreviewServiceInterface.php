<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

/**
 * Defines the contract for catalog move preview service.
 */
interface CatalogMovePreviewServiceInterface
{
    /**
     * @return array{newPath:string,newDepth:int,conflict:bool}|null
     */
    public function preview(string $sourceId, string $targetParentId): ?array;
}
