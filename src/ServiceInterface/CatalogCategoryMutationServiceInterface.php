<?php

declare(strict_types=1);

namespace App\ServiceInterface;

use App\Dto\CatalogCategoryMoveMutationResult;
use App\Dto\CatalogCategoryPublishMutationResult;
use App\ValueObject\CatalogCategoryMutationMoveRequest;
use App\ValueObject\CatalogCategoryMutationPublishRequest;

/**
 * Defines the contract for category mutation service.
 */
interface CatalogCategoryMutationServiceInterface
{
    public function move(CatalogCategoryMutationMoveRequest $request): CatalogCategoryMoveMutationResult;

    public function publish(CatalogCategoryMutationPublishRequest $request): CatalogCategoryPublishMutationResult;
}
