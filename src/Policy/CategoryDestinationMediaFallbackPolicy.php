<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\PolicyInterface\CategoryDestinationMediaFallbackPolicyInterface;
use App\ValueObject\CategoryDestinationMediaFallbackReport;
use App\ValueObjectInterface\CategoryDestinationMediaFallbackReportInterface;

final class CategoryDestinationMediaFallbackPolicy implements CategoryDestinationMediaFallbackPolicyInterface
{
    public function buildReport(string $destinationId, string $categoryId, array $destinationSettings, array $bindings): CategoryDestinationMediaFallbackReportInterface
    {
        $channel = trim((string) ($destinationSettings['channel'] ?? ''));
        $locale = trim((string) ($destinationSettings['locale'] ?? ''));
        $requiredRoles = array_values(array_filter(
            array_map(static fn (mixed $role): string => trim((string) $role), is_array($destinationSettings['requiredMediaRoles'] ?? null) ? $destinationSettings['requiredMediaRoles'] : []),
            static fn (string $role): bool => '' !== $role,
        ));

        $exactByRole = [];
        $fallbackByRole = [];
        $exactMatchedBindingIds = [];
        $fallbackMatchedBindingIds = [];
        $checks = [
            'exactDestinationMediaReady' => true,
            'sharedChannelFallbackReady' => false,
            'sharedLocaleFallbackReady' => false,
            'globalSharedFallbackReady' => false,
            'fallbackCoverageReady' => true,
            'destinationMediaReadyWithFallback' => true,
            'fallbackUsed' => false,
        ];

        foreach ($bindings as $binding) {
            if (!$binding instanceof CategoryMediaBindingInterface || !$binding->active()) {
                continue;
            }

            $role = $binding->role()->value();
            if ([] !== $requiredRoles && !in_array($role, $requiredRoles, true)) {
                continue;
            }

            $channelKind = $this->channelKind($binding, $channel);
            $localeKind = $this->localeKind($binding, $locale);
            if (null === $channelKind || null === $localeKind) {
                continue;
            }

            $bindingId = $binding->bindingId();
            if ('exact' === $channelKind && 'exact' === $localeKind) {
                $exactByRole[$role] = $bindingId;
                $exactMatchedBindingIds[] = $bindingId;
                continue;
            }

            $fallbackByRole[$role] ??= $bindingId;
            $fallbackMatchedBindingIds[] = $bindingId;

            if ('shared' === $channelKind && 'exact' === $localeKind) {
                $checks['sharedChannelFallbackReady'] = true;
            }
            if ('exact' === $channelKind && 'shared' === $localeKind) {
                $checks['sharedLocaleFallbackReady'] = true;
            }
            if ('shared' === $channelKind && 'shared' === $localeKind) {
                $checks['globalSharedFallbackReady'] = true;
            }
        }

        $requiredMissing = [];
        $warnings = [];

        foreach ($requiredRoles as $role) {
            $hasExact = isset($exactByRole[$role]);
            $hasFallback = isset($fallbackByRole[$role]);

            if (!$hasExact) {
                $checks['exactDestinationMediaReady'] = false;
            }
            if (!$hasExact && !$hasFallback) {
                $checks['fallbackCoverageReady'] = false;
                $checks['destinationMediaReadyWithFallback'] = false;
                $requiredMissing[] = sprintf('destination_required_role:%s', $role);
            }
            if (!$hasExact && $hasFallback) {
                $checks['fallbackUsed'] = true;
                $warnings[] = sprintf('fallback_used_for_role:%s', $role);
            }
        }

        if ([] === $requiredRoles) {
            $warnings[] = 'requiredMediaRolesNotSpecified';
        }

        if (!$checks['exactDestinationMediaReady']) {
            $warnings[] = 'exactDestinationMediaMissing';
        }
        if ($checks['fallbackUsed']) {
            $warnings[] = 'sharedFallbackUsed';
        }

        $requiredMissing = array_values(array_unique($requiredMissing));
        $warnings = array_values(array_unique($warnings));
        sort($requiredMissing);
        sort($warnings);
        $exactMatchedBindingIds = array_values(array_unique($exactMatchedBindingIds));
        $fallbackMatchedBindingIds = array_values(array_unique($fallbackMatchedBindingIds));
        sort($exactMatchedBindingIds);
        sort($fallbackMatchedBindingIds);

        return new CategoryDestinationMediaFallbackReport(
            $checks,
            $requiredMissing,
            $warnings,
            $exactMatchedBindingIds,
            $fallbackMatchedBindingIds,
            (bool) $checks['exactDestinationMediaReady'],
            (bool) $checks['destinationMediaReadyWithFallback'],
        );
    }

    private function channelKind(CategoryMediaBindingInterface $binding, string $channel): ?string
    {
        if ('' === $channel) {
            return 'exact';
        }

        $channels = $binding->channels();
        if ([] === $channels) {
            return 'shared';
        }

        return in_array($channel, $channels, true) ? 'exact' : null;
    }

    private function localeKind(CategoryMediaBindingInterface $binding, string $locale): ?string
    {
        if ('' === $locale) {
            return 'exact';
        }

        $locales = $binding->locales();
        if ([] === $locales) {
            return 'shared';
        }

        return in_array($locale, $locales, true) ? 'exact' : null;
    }
}
