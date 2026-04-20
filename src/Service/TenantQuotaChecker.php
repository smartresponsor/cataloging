<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the tenant quota checker application service.
 */
final readonly class TenantQuotaChecker
{
    /** @param array{max_categories?: int, max_depth?: int, max_updates_per_day?: int} $limits */
    public function __construct(private array $limits = [
        'max_categories' => 500,
        'max_depth' => 6,
        'max_updates_per_day' => 200,
    ])
    {
    }

    /**
     * @param array{count?: int, depth?: int, updates_today?: int} $stats
     *
     * @return list<string>
     */
    public function check(string $tenantId, array $stats): array
    {
        unset($tenantId);

        $violations = [];
        if (($stats['count'] ?? 0) > ($this->limits['max_categories'] ?? 500)) {
            $violations[] = 'max_categories';
        }
        if (($stats['depth'] ?? 0) > ($this->limits['max_depth'] ?? 6)) {
            $violations[] = 'max_depth';
        }
        if (($stats['updates_today'] ?? 0) > ($this->limits['max_updates_per_day'] ?? 200)) {
            $violations[] = 'max_updates_per_day';
        }

        return $violations;
    }
}
