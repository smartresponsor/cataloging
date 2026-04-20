<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the warmup dispatcher application service.
 */
final readonly class WarmupDispatcher
{
    /**
     * Initializes the warmup dispatcher service collaborators.
     */
    public function __construct(
        private WarmupPlan $plan,
        private CloudflarePurger $cf,
        private FastlyPurger $fastly,
    ) {
    }

    /** @return array{urls:list<string>,cloudflare:array<string,mixed>,fastly:string} */
    public function dispatchPublish(string $categoryId): array
    {
        $urls = $this->plan->planForPublish($categoryId);
        $keys = array_map(static fn (string $url): string => 'url:'.$url, $urls);

        return [
            'urls' => $urls,
            'cloudflare' => $this->cf->payloadForKeys($keys),
            'fastly' => $this->fastly->headerForKeys($keys),
        ];
    }
}
