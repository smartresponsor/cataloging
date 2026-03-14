<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Security\Category;

use App\ServiceInterface\Security\Category\CacheStoreInterface;

class TokenBucket
{
    public function __construct(
        private readonly CacheStoreInterface $store,
        private readonly string $key,
        private readonly int $capacity,
        private readonly float $ratePerSec,
    ) {
    }

    public function take(int $n = 1): bool
    {
        $now = microtime(true);
        $stateRaw = $this->store->get($this->key);
        $state = is_string($stateRaw) ? json_decode($stateRaw, true) : null;
        if (!is_array($state)) {
            $state = ['t' => $now, 'v' => $this->capacity];
        }

        $elapsed = max(0.0, $now - (float) ($state['t'] ?? $now));
        $refill = (float) ($state['v'] ?? $this->capacity) + ($elapsed * $this->ratePerSec);
        $value = (int) min($this->capacity, floor($refill));

        if ($value < $n) {
            $this->store->set($this->key, json_encode(['t' => $now, 'v' => $value], JSON_THROW_ON_ERROR), 60);

            return false;
        }

        $value -= $n;
        $this->store->set($this->key, json_encode(['t' => $now, 'v' => $value], JSON_THROW_ON_ERROR), 60);

        return true;
    }
}
