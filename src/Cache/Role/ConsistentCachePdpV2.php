<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Cache\Role;

/**
 * Backward-compatible cached PDP bridge for older role consistency tests/callers.
 *
 * The implementation is intentionally tolerant: it accepts a wide range of
 * optional callbacks and delegates to an inner PDP object when available.
 */
final readonly class ConsistentCachePdpV2
{
    private mixed $inner;
    private mixed $cacheKeyFn;
    private mixed $cacheGetFn;
    private mixed $cacheSetFn;
    private mixed $cacheDropFn;
    private mixed $policyTokenFn;
    private mixed $rebacTokenFn;
    private mixed $tokenDropFn;
    private mixed $composeFn;

    /** @var list<mixed> */
    private array $extraCallbacks;

    /**
     * Initializes the consistent cache pdp v2 service collaborators.
     */
    public function __construct(
        mixed $inner = null,
        mixed $cacheKeyFn = null,
        mixed $cacheGetFn = null,
        mixed $cacheSetFn = null,
        mixed $cacheDropFn = null,
        mixed $policyTokenFn = null,
        mixed $rebacTokenFn = null,
        mixed $tokenDropFn = null,
        mixed $composeFn = null,
        mixed ...$extraCallbacks,
    ) {
        $this->inner = $inner;
        $this->cacheKeyFn = $cacheKeyFn;
        $this->cacheGetFn = $cacheGetFn;
        $this->cacheSetFn = $cacheSetFn;
        $this->cacheDropFn = $cacheDropFn;
        $this->policyTokenFn = $policyTokenFn;
        $this->rebacTokenFn = $rebacTokenFn;
        $this->tokenDropFn = $tokenDropFn;
        $this->composeFn = $composeFn;
        $this->extraCallbacks = array_values($extraCallbacks);
    }

    /**
     * Executes the invokable workflow for this service.
     */
    public function __invoke(mixed ...$arguments): mixed
    {
        return $this->evaluate(...$arguments);
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(mixed ...$arguments): mixed
    {
        $cacheKey = $this->resolveCacheKey(...$arguments);

        if (null !== $cacheKey && is_callable($this->cacheGetFn)) {
            $cached = ($this->cacheGetFn)($cacheKey, ...$arguments);
            if (null !== $cached) {
                return $cached;
            }
        }

        $result = $this->delegate(...$arguments);

        if (null !== $cacheKey && is_callable($this->cacheSetFn)) {
            ($this->cacheSetFn)($cacheKey, $result, ...$arguments);
        }

        return $result;
    }

    /**
     * Handles the decide workflow.
     */
    public function decide(mixed ...$arguments): mixed
    {
        return $this->evaluate(...$arguments);
    }

    /**
     * Handles the authorize workflow.
     */
    public function authorize(mixed ...$arguments): mixed
    {
        return $this->evaluate(...$arguments);
    }

    /**
     * @return array{tokenEvents:list<string>,cacheKeys:list<string>}
     */
    public function invalidate(mixed ...$arguments): array
    {
        $tokens = [];

        foreach ([$this->policyTokenFn, $this->rebacTokenFn] as $callback) {
            if (!is_callable($callback)) {
                continue;
            }

            $value = $callback(...$arguments);
            if (is_iterable($value)) {
                foreach ($value as $token) {
                    if (is_scalar($token) || $token instanceof \Stringable) {
                        $tokens[] = (string) $token;
                    }
                }
            } elseif (is_scalar($value) || $value instanceof \Stringable) {
                $tokens[] = (string) $value;
            }
        }

        if (is_callable($this->tokenDropFn)) {
            ($this->tokenDropFn)($tokens, ...$arguments);
        }

        if (is_callable($this->cacheDropFn)) {
            ($this->cacheDropFn)($this->resolveCacheKey(...$arguments), $tokens, ...$arguments);
        }

        return [
            'tokenEvents' => array_values(array_unique($tokens)),
            'cacheKeys' => $this->stringCacheKeys($this->resolveCacheKey(...$arguments)),
        ];
    }

    private function resolveCacheKey(mixed ...$arguments): string|int|float|bool|null
    {
        if (!is_callable($this->cacheKeyFn)) {
            return null;
        }

        $value = ($this->cacheKeyFn)(...$arguments);

        return is_scalar($value) ? $value : ($value instanceof \Stringable ? (string) $value : null);
    }

    /** @return list<string> */
    private function stringCacheKeys(string|int|float|bool|null $cacheKey): array
    {
        if (null === $cacheKey) {
            return [];
        }

        $normalized = (string) $cacheKey;

        return '' === $normalized ? [] : [$normalized];
    }

    private function delegate(mixed ...$arguments): mixed
    {
        if (is_callable($this->composeFn)) {
            return ($this->composeFn)(...$arguments);
        }

        if (is_callable($this->inner)) {
            return ($this->inner)(...$arguments);
        }

        if (is_object($this->inner)) {
            foreach (['evaluate', 'decide', 'authorize', '__invoke', 'handle', 'run'] as $method) {
                if (method_exists($this->inner, $method)) {
                    return $this->inner->{$method}(...$arguments);
                }
            }
        }

        foreach ($this->extraCallbacks as $callback) {
            if (is_callable($callback)) {
                return $callback(...$arguments);
            }
        }

        return true;
    }
}
