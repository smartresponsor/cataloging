<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\CatalogCategory\Quota;

final class TokenBucket
{
    private string $key;
    private int $capacity;
    private float $ratePerSec;
    private CacheStoreInterface $store;

    public function __construct(CacheStoreInterface $store, string $key, int $capacity, float $ratePerSec)
    {
        $this->store = $store;
        $this->key = $key;
        $this->capacity = $capacity;
        $this->ratePerSec = $ratePerSec;
    }

    public function take(int $n = 1): bool
    {
        $now = microtime(true);
        $stateRaw = $this->store->get($this->key);
        $state = $stateRaw ? json_decode($stateRaw, true) : ['t' => $now, 'v' => $this->capacity];
        $elapsed = max(0.0, $now - (float) $state['t']);
        $refill = (float) $state['v'] + $elapsed * $this->ratePerSec;
        $value = (int) min($this->capacity, floor($refill));
        if ($value < $n) {
            $this->store->set($this->key, json_encode(['t' => $now, 'v' => $value]), 60);

            return false;
        }
        $value -= $n;
        $this->store->set($this->key, json_encode(['t' => $now, 'v' => $value]), 60);

        return true;
    }
}
