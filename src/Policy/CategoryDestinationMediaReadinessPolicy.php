<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryDestinationMediaReadinessPolicyInterface;
use App\ValueObject\CategoryDestinationMediaReadinessReport;
use App\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

final class CategoryDestinationMediaReadinessPolicy implements CategoryDestinationMediaReadinessPolicyInterface
{
    /**
     * @param array<string,mixed> $destinationSettings
     * @param array<string,mixed> $applicabilityPayload
     * @param array<string,bool>  $checks
     * @param list<string>        $requiredMissing
     * @param list<string>        $warnings
     * @param list<string>        $matchedBindingIds
     */
    public function buildReport(string $destinationId, string $categoryId, array $destinationSettings, array $applicabilityPayload, array $checks, array $requiredMissing, array $warnings, array $matchedBindingIds): CategoryDestinationMediaReadinessReportInterface
    {
        $channel = $this->stringValue($destinationSettings['channel'] ?? null);
        $locale = $this->stringValue($destinationSettings['locale'] ?? null);
        $requiredRoles = $this->stringList($destinationSettings['requiredMediaRoles'] ?? null);

        $checks['destinationChannelMediaReady'] = '' === $channel || (bool) ($checks['channelScopedMediaReady'] ?? false);
        $checks['destinationLocaleMediaReady'] = '' === $locale || (bool) ($checks['localeScopedMediaReady'] ?? false);
        $checks['destinationRequiredRolesReady'] = [] === $requiredRoles || (bool) ($checks['requiredRoleCoverageReady'] ?? false);
        $checks['destinationScopedExactMatchReady'] = (bool) ($checks['exactChannelLocaleMatchReady'] ?? false);
        $checks['destinationMediaPublishable'] = (bool) $checks['destinationChannelMediaReady']
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
        $matchedBindingIds = array_values(array_unique(array_filter($matchedBindingIds, static fn (mixed $v): bool => is_string($v) && '' !== trim($v))));
        sort($requiredMissing);
        sort($warnings);

        return new CategoryDestinationMediaReadinessReport($checks, $requiredMissing, $warnings, $matchedBindingIds, (bool) $checks['destinationMediaPublishable']);
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        $items = [];
        if (is_array($value)) {
            $items = $value;
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = preg_split('/\s*,\s*/', $value) ?: [];
            }
        } elseif (is_scalar($value)) {
            $items = [(string) $value];
        } else {
            return [];
        }

        $result = [];
        foreach ($items as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $normalized = trim((string) $item);
            if ('' === $normalized) {
                continue;
            }
            $result[] = $normalized;
        }

        return array_values(array_unique($result));
    }
}
