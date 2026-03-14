<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Security\Category;

use App\ServiceInterface\Security\Category\CacheStoreInterface;

class QuotaService
{
    public function __construct(private readonly CacheStoreInterface $store)
    {
    }

    public function allow(string $scope, string $id, string $op, int $capacity, float $ratePerSec): bool
    {
        $key = 'quota:'.$scope.':'.$id.':'.$op;
        $bucket = new TokenBucket($this->store, $key, $capacity, $ratePerSec);

        return $bucket->take(1);
    }
}
