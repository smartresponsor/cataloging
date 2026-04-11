<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Quota;

use App\ServiceInterface\Category\CategoryQuotaServiceInterface;
use App\ServiceInterface\Quota\CacheStoreInterface;
use App\ValueObject\CategoryQuotaAllowanceRequest;

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
    public function allow(CategoryQuotaAllowanceRequest $request): bool
    {
        $key = 'quota:'.$request->scope().':'.$request->id().':'.$request->operation();
        $bucket = new CategoryTokenBucket($this->store, $key, $request->capacity(), $request->ratePerSecond());

        return $bucket->take();
    }
}
