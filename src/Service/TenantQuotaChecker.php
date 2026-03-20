<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class TenantQuotaChecker
{
    public function __construct(private readonly array $limits = [
        'max_categories' => 500,
        'max_depth' => 6,
        'max_updates_per_day' => 200,
    ])
    {
    }

    public function check(string $tenant, array $stats): array
    {
        $violations = [];
        if (($stats['count'] ?? 0) > $this->limits['max_categories']) {
            $violations[] = 'max_categories';
        }
        if (($stats['depth'] ?? 0) > $this->limits['max_depth']) {
            $violations[] = 'max_depth';
        }
        if (($stats['updates_today'] ?? 0) > $this->limits['max_updates_per_day']) {
            $violations[] = 'max_updates_per_day';
        }

        return $violations;
    }
}
