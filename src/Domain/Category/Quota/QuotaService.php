<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\CatalogCategory\Domain\Quota;

final class QuotaService
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
