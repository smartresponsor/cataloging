<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Consistency\Role;

if (!interface_exists('PolicyInterface\Role\PdpV2Interface', false)) {
    /**
     * Defines the contract for pdp v2.
     */
    eval('namespace PolicyInterface\Role; interface PdpV2Interface {}');
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
     * @var array
     *
     * @phpstan-var list<mixed>
     */
    private array $participants;

    /**
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     *
     * @param array $participants
     *
     * @phpstan-param iterable<mixed> $participants
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

        /** @var list<mixed> $participantList */
        $participantList = is_array($participants)
            ? [...$participants]
            : iterator_to_array($participants, false);

        $this->participants = $participantList;

        $participants = [
            $composeFn,
            $policyTokenFn,
            $cacheKeyFn,
            $invalidateCacheFn,
            $tokenInvalidatorFn,
            ...$extraCallbacks,
        ];

        foreach ($participants as $participant) {
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
            }
        }

        return [
            'tokenEvents' => $tokenEvents,
            'cacheKeys' => $cacheKeys,
            'results' => $results,
        ];
    }

    private function runParticipant(mixed $participant, mixed ...$arguments): mixed
    {
        if (is_callable($participant)) {
            return $participant(...$arguments);
        }

        if (is_object($participant) && method_exists($participant, 'compose')) {
            return $participant->compose(...$arguments);
        }

        if (is_object($participant) && method_exists($participant, '__invoke')) {
            return $participant(...$arguments);
        }

        return null;
    }
}
