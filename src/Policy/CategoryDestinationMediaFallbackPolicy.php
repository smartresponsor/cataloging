<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryMediaBindingEntityInterface;
use App\Cataloging\PolicyInterface\CategoryDestinationMediaFallbackPolicyInterface;
use App\Cataloging\Service\CategoryMediaInputNormalizer;
use App\Cataloging\ValueObject\CategoryDestinationMediaFallbackReport;
use App\Cataloging\ValueObjectInterface\CategoryDestinationMediaFallbackReportInterface;

/**
 * Provides the category destination media fallback policy implementation.
 */
/** @noinspection PhpUnusedLocalVariableInspection */
final class CategoryDestinationMediaFallbackPolicy implements CategoryDestinationMediaFallbackPolicyInterface
{
    /**
     * @param array<string,mixed>                                   $destinationSettings
     * @param array<int,CatalogCategoryMediaBindingEntityInterface> $bindings
     */
    public function buildReport(
        string $destinationId,
        string $categoryId,
        array $destinationSettings,
        array $bindings,
    ): CategoryDestinationMediaFallbackReportInterface {
        $channel = CategoryMediaInputNormalizer::stringValue($destinationSettings['channel'] ?? null);
        $locale = CategoryMediaInputNormalizer::stringValue($destinationSettings['locale'] ?? null);
        $requiredRoles = CategoryMediaInputNormalizer::stringList($destinationSettings['requiredMediaRoles'] ?? null);

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
            if (!$binding->active()) {
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
            $checks['exactDestinationMediaReady'],
            $checks['destinationMediaReadyWithFallback'],
        );
    }

    private function channelKind(CatalogCategoryMediaBindingEntityInterface $binding, string $channel): ?string
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

    private function localeKind(CatalogCategoryMediaBindingEntityInterface $binding, string $locale): ?string
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
