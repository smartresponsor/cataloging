<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Category\Quota;

use App\Cataloging\ServiceInterface\Category\CategoryQuotaTokenBucketInterface;
use App\Cataloging\ServiceInterface\Quota\CacheStoreInterface;

/**
 * Provides the category token bucket application service.
 */
/** @noinspection PhpPropertyNamingConventionInspection */
final class CategoryTokenBucket implements CategoryQuotaTokenBucketInterface
{
    private string $key;
    private int $capacity;
    private float $ratePerSec;
    private CacheStoreInterface $store;

    /**
     * Initializes the category token bucket service collaborators.
     */
    public function __construct(CacheStoreInterface $store, string $key, int $capacity, float $ratePerSec)
    {
        $this->store = $store;
        $this->key = $key;
        $this->capacity = $capacity;
        $this->ratePerSec = $ratePerSec;
    }

    /**
     * Handles the take workflow.
     */
    public function take(int $n = 1): bool
    {
        $now = microtime(true);
        $stateRaw = $this->store->get($this->key);
        $decoded = is_string($stateRaw) ? json_decode($stateRaw, true) : null;
        $state = is_array($decoded) ? $decoded : ['t' => $now, 'v' => $this->capacity];
        $elapsed = max(0.0, $now - $this->floatValue($state['t'] ?? $now, $now));
        $refill = $this->floatValue($state['v'] ?? $this->capacity, (float) $this->capacity) + $elapsed * $this->ratePerSec;
        $value = (int) min($this->capacity, floor($refill));
        if ($value < $n) {
            $encoded = json_encode(['t' => $now, 'v' => $value]);
            $this->store->set($this->key, false === $encoded ? '{"t":0,"v":0}' : $encoded, 60);

            return false;
        }
        $value -= $n;
        $encoded = json_encode(['t' => $now, 'v' => $value]);
        $this->store->set($this->key, false === $encoded ? '{"t":0,"v":0}' : $encoded, 60);

        return true;
    }

    private function floatValue(mixed $value, float $default): float
    {
        return is_numeric($value) ? (float) $value : $default;
    }
}
