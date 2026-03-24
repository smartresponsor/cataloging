<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Quota;

use App\ServiceInterface\Quota\CacheStoreInterface;

final class CatalogQuotaService
{
    private CacheStoreInterface $store;

    public function __construct(CacheStoreInterface $store)
    {
        $this->store = $store;
    }

    public function allow(string $scope, string $id, string $op, int $capacity, float $ratePerSec): bool
    {
        $key = 'quota:'.$scope.':'.$id.':'.$op;
        $bucket = new TokenBucket($this->store, $key, $capacity, $ratePerSec);

        return $bucket->take(1);
    }
}
