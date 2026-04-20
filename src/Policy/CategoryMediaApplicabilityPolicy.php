<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\EntityInterface\CategoryMediaBindingInterface;
use App\Cataloging\PolicyInterface\CategoryMediaApplicabilityPolicyInterface;
use App\Cataloging\Service\CategoryMediaInputNormalizer;
use App\Cataloging\ValueObject\CategoryMediaApplicabilityReport;
use App\Cataloging\ValueObjectInterface\CategoryMediaApplicabilityReportInterface;

/**
 * Provides the category media applicability policy implementation.
 */
final class CategoryMediaApplicabilityPolicy implements CategoryMediaApplicabilityPolicyInterface
{
    /**
     * @param array<string,mixed>                      $payload
     * @param array<int,CategoryMediaBindingInterface> $bindings
     */
    public function buildReport(array $payload, array $bindings): CategoryMediaApplicabilityReportInterface
    {
        $channel = CategoryMediaInputNormalizer::stringValue($payload['channel'] ?? null);
        $locale = CategoryMediaInputNormalizer::stringValue($payload['locale'] ?? null);
        $requiredRoles = CategoryMediaInputNormalizer::stringList($payload['requiredRoles'] ?? null);

        $matched = [];
        $matchedRoles = [];
        $exactMatches = [];
        foreach ($bindings as $binding) {
            if (!$binding->active()) {
                continue;
            }
            if (!$this->matchesChannel($binding, $channel) || !$this->matchesLocale($binding, $locale)) {
                continue;
            }
            $matched[] = $binding;
            $matchedRoles[$binding->role()->value()] = true;
            if ($this->isExactChannelMatch($binding, $channel) && $this->isExactLocaleMatch($binding, $locale)) {
                $exactMatches[] = $binding;
            }
        }

        $checks = [
            'channelScopedMediaReady' => [] !== $matched || '' === $channel,
            'localeScopedMediaReady' => [] !== $matched || '' === $locale,
            'requiredRoleCoverageReady' => true,
            'exactChannelLocaleMatchReady' => [] !== $exactMatches || ('' === $channel && '' === $locale),
        ];

        $requiredMissing = [];
        foreach ($requiredRoles as $role) {
            if (($matchedRoles[$role] ?? false) !== true) {
                $checks['requiredRoleCoverageReady'] = false;
                $requiredMissing[] = 'role:'.$role;
            }
        }
        if ('' !== $channel && !$checks['channelScopedMediaReady']) {
            $requiredMissing[] = 'channelScopedMediaReady';
        }
        if ('' !== $locale && !$checks['localeScopedMediaReady']) {
            $requiredMissing[] = 'localeScopedMediaReady';
        }

        $warnings = [];
        if (!$checks['exactChannelLocaleMatchReady']) {
            $warnings[] = 'exactChannelLocaleMatchReady';
        }
        if ([] === $requiredRoles) {
            $warnings[] = 'requiredRolesNotSpecified';
        }

        return new CategoryMediaApplicabilityReport(
            $checks,
            array_values(array_unique($requiredMissing)),
            $warnings,
            array_map(
                static fn (CategoryMediaBindingInterface $binding): string => $binding->bindingId(),
                $matched,
            ),
        );
    }

    private function matchesChannel(CategoryMediaBindingInterface $binding, string $channel): bool
    {
        if ('' === $channel) {
            return true;
        }
        $channels = $binding->channels();

        return [] === $channels || in_array($channel, $channels, true);
    }

    private function matchesLocale(CategoryMediaBindingInterface $binding, string $locale): bool
    {
        if ('' === $locale) {
            return true;
        }
        $locales = $binding->locales();

        return [] === $locales || in_array($locale, $locales, true);
    }

    private function isExactChannelMatch(CategoryMediaBindingInterface $binding, string $channel): bool
    {
        if ('' === $channel) {
            return true;
        }
        $channels = $binding->channels();

        return [] !== $channels && in_array($channel, $channels, true);
    }

    private function isExactLocaleMatch(CategoryMediaBindingInterface $binding, string $locale): bool
    {
        if ('' === $locale) {
            return true;
        }
        $locales = $binding->locales();

        return [] !== $locales && in_array($locale, $locales, true);
    }
}
