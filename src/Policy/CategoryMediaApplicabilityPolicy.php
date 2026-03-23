<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\PolicyInterface\CategoryMediaApplicabilityPolicyInterface;
use App\ValueObject\CategoryMediaApplicabilityReport;
use App\ValueObjectInterface\CategoryMediaApplicabilityReportInterface;

final class CategoryMediaApplicabilityPolicy implements CategoryMediaApplicabilityPolicyInterface
{
    public function buildReport(array $payload, array $bindings): CategoryMediaApplicabilityReportInterface
    {
        $channel = trim((string) ($payload['channel'] ?? ''));
        $locale = trim((string) ($payload['locale'] ?? ''));
        $requiredRoles = array_values(array_filter(
            array_map(static fn (mixed $role): string => trim((string) $role), is_array($payload['requiredRoles'] ?? null) ? $payload['requiredRoles'] : []),
            static fn (string $role): bool => '' !== $role,
        ));

        $matched = [];
        $matchedRoles = [];
        $exactMatches = [];
        foreach ($bindings as $binding) {
            if (!$binding instanceof CategoryMediaBindingInterface || !$binding->active()) {
                continue;
            }
            if (!$this->matchesChannel($binding, $channel)) {
                continue;
            }
            if (!$this->matchesLocale($binding, $locale)) {
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

        if ('' !== $channel && ($checks['channelScopedMediaReady'] ?? false) !== true) {
            $requiredMissing[] = 'channelScopedMediaReady';
        }
        if ('' !== $locale && ($checks['localeScopedMediaReady'] ?? false) !== true) {
            $requiredMissing[] = 'localeScopedMediaReady';
        }

        $warnings = [];
        if (($checks['exactChannelLocaleMatchReady'] ?? false) !== true) {
            $warnings[] = 'exactChannelLocaleMatchReady';
        }
        if ([] === $requiredRoles) {
            $warnings[] = 'requiredRolesNotSpecified';
        }

        return new CategoryMediaApplicabilityReport(
            $checks,
            array_values(array_unique($requiredMissing)),
            $warnings,
            array_values(array_map(static fn (CategoryMediaBindingInterface $binding): string => $binding->bindingId(), $matched)),
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
