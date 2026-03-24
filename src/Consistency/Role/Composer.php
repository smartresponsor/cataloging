<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Consistency\Role;

if (!interface_exists(\PolicyInterface\Role\PdpV2Interface::class, false)) {
    eval('namespace PolicyInterface\\Role; interface PdpV2Interface {}');
}

/**
 * Backward-compatible role composer for composite consistency operations.
 *
 * Supports both participant-based composition and named callback injection used
 * by older tests/callers, including named parameters like $policyTokenFn and
 * $cacheKeyFn.
 */
final class Composer
{
    /**
     * @var list<mixed>
     */
    private array $participants;

    /**
     * @param iterable<mixed> $participants
     */
    public function __construct(
        iterable $participants = [],
        ?callable $policyTokenFn = null,
        ?callable $cacheKeyFn = null,
        ?callable $invalidateCacheFn = null,
        ?callable $tokenInvalidatorFn = null,
        ?callable $composeFn = null,
        mixed ...$extraCallbacks,
    ) {
        $extraCallbacks = [];
        if (func_num_args() > 6) {
            $extraCallbacks = array_slice(func_get_args(), 6);
        }

        $this->participants = is_array($participants) ? array_values($participants) : array_values(iterator_to_array($participants, false));

        foreach ([$composeFn, $policyTokenFn, $cacheKeyFn, $invalidateCacheFn, $tokenInvalidatorFn, ...$extraCallbacks] as $participant) {
            if (null !== $participant) {
                $this->participants[] = $participant;
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function compose(mixed ...$arguments): array
    {
        $tokenEvents = [];
        $cacheKeys = [];
        $results = [];

        foreach ($this->participants as $participant) {
            $result = $this->runParticipant($participant, ...$arguments);
            $results[] = $result;

            if (is_array($result)) {
                if (isset($result['tokenEvents']) && is_iterable($result['tokenEvents'])) {
                    foreach ($result['tokenEvents'] as $tokenEvent) {
                        $tokenEvents[] = $tokenEvent;
                    }
                }

                if (isset($result['cacheKeys']) && is_iterable($result['cacheKeys'])) {
                    foreach ($result['cacheKeys'] as $cacheKey) {
                        $cacheKeys[] = $cacheKey;
                    }
                }

                if (isset($result['tokens']) && is_iterable($result['tokens'])) {
                    foreach ($result['tokens'] as $tokenEvent) {
                        $tokenEvents[] = $tokenEvent;
                    }
                }

                if (isset($result['cache']) && is_iterable($result['cache'])) {
                    foreach ($result['cache'] as $cacheKey) {
                        $cacheKeys[] = $cacheKey;
                    }
                }

                continue;
            }

            if (is_iterable($result)) {
                foreach ($result as $value) {
                    if (is_string($value) || is_int($value) || is_float($value) || $value instanceof \Stringable) {
                        $tokenEvents[] = (string) $value;
                    }
                }

                continue;
            }

            if (is_scalar($result) || $result instanceof \Stringable) {
                $tokenEvents[] = (string) $result;
            }
        }

        return [
            'tokenEvents' => array_values(array_unique($this->normalizeScalarList($tokenEvents))),
            'cacheKeys' => array_values(array_unique($this->normalizeScalarList($cacheKeys))),
            'results' => $results,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(mixed ...$arguments): array
    {
        return $this->compose(...$arguments);
    }

    /**
     * @return array<string, mixed>
     */
    public function invalidate(mixed ...$arguments): array
    {
        return $this->compose(...$arguments);
    }

    private function runParticipant(mixed $participant, mixed ...$arguments): mixed
    {
        if (is_callable($participant)) {
            return $participant(...$arguments);
        }

        foreach (['compose', 'invalidate', 'apply', 'run', 'execute', '__invoke'] as $method) {
            if (is_object($participant) && method_exists($participant, $method)) {
                return $participant->{$method}(...$arguments);
            }
        }

        return $participant;
    }

    /**
     * @param iterable<mixed> $values
     *
     * @return list<string>
     */
    private function normalizeScalarList(iterable $values): array
    {
        $normalized = [];

        foreach ($values as $value) {
            if (is_scalar($value) || $value instanceof \Stringable) {
                $normalized[] = (string) $value;
            }
        }

        return $normalized;
    }
}
