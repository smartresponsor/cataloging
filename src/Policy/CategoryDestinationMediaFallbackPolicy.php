<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\PolicyInterface\CategoryDestinationMediaFallbackPolicyInterface;
use App\ValueObject\CategoryDestinationMediaFallbackReport;
use App\ValueObjectInterface\CategoryDestinationMediaFallbackReportInterface;
/**
 * Provides the category destination media fallback policy implementation.
 */
final class CategoryDestinationMediaFallbackPolicy implements CategoryDestinationMediaFallbackPolicyInterface
{
    /**
     * @param array<string,mixed>                      $destinationSettings
     * @param array<int,CategoryMediaBindingInterface> $bindings
     */
    public function buildReport(
        string $destinationId,
        string $categoryId,
        array $destinationSettings,
        array $bindings,
    ): CategoryDestinationMediaFallbackReportInterface
    {
        $channel = $this->stringValue($destinationSettings['channel'] ?? null);
        $locale = $this->stringValue($destinationSettings['locale'] ?? null);
        $requiredRoles = $this->stringList($destinationSettings['requiredMediaRoles'] ?? null);

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
