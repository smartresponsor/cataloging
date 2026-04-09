<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\PolicyInterface\CategoryMediaApplicabilityPolicyInterface;
use App\ValueObject\CategoryMediaApplicabilityReport;
use App\ValueObjectInterface\CategoryMediaApplicabilityReportInterface;
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
        $channel = $this->stringValue($payload['channel'] ?? null);
        $locale = $this->stringValue($payload['locale'] ?? null);
        $requiredRoles = $this->stringList($payload['requiredRoles'] ?? null);

        $matched = [];
        $matchedRoles = [];
        $exactMatches = [];
        foreach ($bindings as $binding) {
            if (!$binding instanceof CategoryMediaBindingInterface || !$binding->active()) {
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
            array_values(array_map(
                static fn (CategoryMediaBindingInterface $binding): string => $binding->bindingId(),
                $matched,
            )),
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
