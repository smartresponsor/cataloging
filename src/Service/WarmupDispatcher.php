<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishченко / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Тishchenko <dev@smartresponsor.com>
*/

namespace App\Service;

use App\Service\Category\WarmupPlan;

final class WarmupDispatcher
{
    private WarmupPlan $plan;
    private CloudflarePurger $cf;
    private FastlyPurger $fastly;

    public function __construct(WarmupPlan $plan, CloudflarePurger $cf, FastlyPurger $fastly)
    {
        $this->plan = $plan;
        $this->cf = $cf;
        $this->fastly = $fastly;
    }

    public function dispatchPublish(string $categoryId): array
    {
        $urls = $this->plan->planForPublish($categoryId);
        $keys = array_map(fn (string $u) => 'url:'.$u, $urls);

        return [
            'urls' => $urls,
            'cloudflare' => $this->cf->payloadForKeys($keys),
            'fastly' => $this->fastly->headerForKeys($keys),
        ];
    }
}
