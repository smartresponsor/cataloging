<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class WarmupDispatcher
{
    public function __construct(
        private readonly WarmupPlan $plan,
        private readonly CloudflarePurger $cf,
        private readonly FastlyPurger $fastly,
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
