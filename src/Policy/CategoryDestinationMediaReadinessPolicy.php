<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryDestinationMediaReadinessPolicyInterface;
use App\ValueObject\CategoryDestinationMediaReadinessContext;
use App\ValueObject\CategoryDestinationMediaReadinessReport;
use App\ValueObject\CategoryDestinationMediaReadinessState;
use App\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

/**
 * Provides the category destination media readiness policy implementation.
 */
final class CategoryDestinationMediaReadinessPolicy implements CategoryDestinationMediaReadinessPolicyInterface
{
    public function buildReport(
        CategoryDestinationMediaReadinessContext $context,
        CategoryDestinationMediaReadinessState $state,
    ): CategoryDestinationMediaReadinessReportInterface {
        $destinationSettings = $context->destinationSettings();
        $checks = $state->checks();
        $requiredMissing = $state->requiredMissing();
        $warnings = $state->warnings();
        $matchedBindingIds = $state->matchedBindingIds();

        $channel = $this->stringValue($destinationSettings['channel'] ?? null);
        $locale = $this->stringValue($destinationSettings['locale'] ?? null);
        $requiredRoles = $this->stringList($destinationSettings['requiredMediaRoles'] ?? null);

        $checks['destinationChannelMediaReady'] =
            '' === $channel
            || ($checks['channelScopedMediaReady'] ?? false);
        $checks['destinationLocaleMediaReady'] = '' === $locale || ($checks['localeScopedMediaReady'] ?? false);
        $checks['destinationRequiredRolesReady'] =
            [] === $requiredRoles
            || ($checks['requiredRoleCoverageReady'] ?? false);
        $checks['destinationScopedExactMatchReady'] = $checks['exactChannelLocaleMatchReady'] ?? false;
        $checks['destinationMediaPublishable'] = $checks['destinationChannelMediaReady']
            && $checks['destinationLocaleMediaReady']
            && $checks['destinationRequiredRolesReady']
            && $checks['destinationScopedExactMatchReady'];

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
        $matchedBindingIds = array_values(
            array_unique(
                array_filter(
                    $matchedBindingIds,
                    static fn (mixed $v): bool => is_string($v) && '' !== trim($v),
                ),
            ),
        );
        sort($requiredMissing);
        sort($warnings);

        return new CategoryDestinationMediaReadinessReport(
            $checks,
            $requiredMissing,
            $warnings,
            $matchedBindingIds,
            $checks['destinationMediaPublishable'],
        );
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
