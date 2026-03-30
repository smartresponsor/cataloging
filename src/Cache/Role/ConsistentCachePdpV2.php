<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cache\Role;

/**
 * Backward-compatible cached PDP bridge for older role consistency tests/callers.
 *
 * The implementation is intentionally tolerant: it accepts a wide range of
 * optional callbacks and delegates to an inner PDP object when available.
 */
final class ConsistentCachePdpV2
{
    private readonly mixed $inner;
    private readonly mixed $cacheKeyFn;
    private readonly mixed $cacheGetFn;
    private readonly mixed $cacheSetFn;
    private readonly mixed $cacheInvalidateFn;
    private readonly mixed $policyTokenFn;
    private readonly mixed $rebacTokenFn;
    private readonly mixed $tokenInvalidatorFn;
    private readonly mixed $composeFn;

    /** @var list<mixed> */
    private readonly array $extraCallbacks;

    public function __construct(
        mixed $inner = null,
        mixed $cacheKeyFn = null,
        mixed $cacheGetFn = null,
        mixed $cacheSetFn = null,
        mixed $cacheInvalidateFn = null,
        mixed $policyTokenFn = null,
        mixed $rebacTokenFn = null,
        mixed $tokenInvalidatorFn = null,
        mixed $composeFn = null,
        mixed ...$extraCallbacks,
    ) {
        $this->inner = $inner;
        $this->cacheKeyFn = $cacheKeyFn;
        $this->cacheGetFn = $cacheGetFn;
        $this->cacheSetFn = $cacheSetFn;
        $this->cacheInvalidateFn = $cacheInvalidateFn;
        $this->policyTokenFn = $policyTokenFn;
        $this->rebacTokenFn = $rebacTokenFn;
        $this->tokenInvalidatorFn = $tokenInvalidatorFn;
        $this->composeFn = $composeFn;
        $this->extraCallbacks = array_values($extraCallbacks);
    }

    public function __invoke(mixed ...$arguments): mixed
    {
        return $this->evaluate(...$arguments);
    }

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

    public function decide(mixed ...$arguments): mixed
    {
        return $this->evaluate(...$arguments);
    }

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

        if (is_callable($this->tokenInvalidatorFn)) {
            ($this->tokenInvalidatorFn)($tokens, ...$arguments);
        }

        if (is_callable($this->cacheInvalidateFn)) {
            ($this->cacheInvalidateFn)($this->resolveCacheKey(...$arguments), $tokens, ...$arguments);
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
