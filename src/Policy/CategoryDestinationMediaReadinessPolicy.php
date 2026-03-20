<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Policy;

use App\PolicyInterface\CategoryDestinationMediaReadinessPolicyInterface;
use App\ValueObject\CategoryDestinationMediaReadinessReport;
use App\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

final class CategoryDestinationMediaReadinessPolicy implements CategoryDestinationMediaReadinessPolicyInterface
{
    public function buildReport(string $destinationId, string $categoryId, array $destinationSettings, array $applicabilityPayload, array $checks, array $requiredMissing, array $warnings, array $matchedBindingIds): CategoryDestinationMediaReadinessReportInterface
    {
        $channel = trim((string) ($destinationSettings['channel'] ?? ''));
        $locale = trim((string) ($destinationSettings['locale'] ?? ''));
        $requiredRoles = array_values(array_filter(
            array_map(static fn (mixed $role): string => trim((string) $role), is_array($destinationSettings['requiredMediaRoles'] ?? null) ? $destinationSettings['requiredMediaRoles'] : []),
            static fn (string $role): bool => '' !== $role,
        ));

        $checks['destinationChannelMediaReady'] = '' === $channel || (bool) ($checks['channelScopedMediaReady'] ?? false);
        $checks['destinationLocaleMediaReady'] = '' === $locale || (bool) ($checks['localeScopedMediaReady'] ?? false);
        $checks['destinationRequiredRolesReady'] = [] === $requiredRoles || (bool) ($checks['requiredRoleCoverageReady'] ?? false);
        $checks['destinationScopedExactMatchReady'] = (bool) ($checks['exactChannelLocaleMatchReady'] ?? false);
        $checks['destinationMediaPublishable'] =
            (bool) $checks['destinationChannelMediaReady']
            && (bool) $checks['destinationLocaleMediaReady']
            && (bool) $checks['destinationRequiredRolesReady']
            && (bool) $checks['destinationScopedExactMatchReady'];

        if (!$checks['destinationChannelMediaReady']) {
            $requiredMissing[] = 'destination_channel_media';
        }

        if (!$checks['destinationLocaleMediaReady']) {
            $requiredMissing[] = 'destination_locale_media';
        }

        if (!$checks['destinationRequiredRolesReady']) {
            foreach ($requiredRoles as $role) {
                $requiredMissing[] = sprintf('destination_required_role:%s', $role);
            }
        }

        if (!$checks['destinationScopedExactMatchReady']) {
            $warnings[] = 'destination_exact_match_missing';
        }

        $requiredMissing = array_values(array_unique($requiredMissing));
        $warnings = array_values(array_unique($warnings));
        sort($requiredMissing);
        sort($warnings);

        return new CategoryDestinationMediaReadinessReport($checks, $requiredMissing, $warnings, array_values(array_unique($matchedBindingIds)), (bool) $checks['destinationMediaPublishable']);
    }
}
