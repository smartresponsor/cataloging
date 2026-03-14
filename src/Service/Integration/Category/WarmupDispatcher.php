<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
*/

namespace App\Service\Integration\Category;

final class WarmupDispatcher
{
    public function __construct(
        private WarmupPlan $plan,
        private CloudflarePurger $cloudflarePurger,
        private FastlyPurger $fastlyPurger,
    ) {
    }

    public function dispatchPublish(string $categoryId): array
    {
        $urls = $this->plan->planForPublish($categoryId);
        $keys = array_map(static fn (string $url): string => 'url:'.$url, $urls);

        return [
            'urls' => $urls,
            'cloudflare' => $this->cloudflarePurger->payloadForKeys($keys),
            'fastly' => $this->fastlyPurger->headerForKeys($keys),
        ];
    }
}
