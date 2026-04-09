<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Quota;

use App\ServiceInterface\Category\CategoryQuotaServiceInterface;
use App\ServiceInterface\Quota\CacheStoreInterface;

/**
 * Provides the category quota service application service.
 */
final class CategoryQuotaService implements CategoryQuotaServiceInterface
{
    private CacheStoreInterface $store;

    /**
     * Initializes the category quota service service collaborators.
     */
    public function __construct(CacheStoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * Handles the allow workflow.
     */
    public function allow(string $scope, string $id, string $op, int $capacity, float $ratePerSec): bool
    {
        $key = 'quota:'.$scope.':'.$id.':'.$op;
        $bucket = new CategoryTokenBucket($this->store, $key, $capacity, $ratePerSec);

        return $bucket->take();
    }
}
