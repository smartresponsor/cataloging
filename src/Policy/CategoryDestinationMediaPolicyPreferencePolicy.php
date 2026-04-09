<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\PolicyInterface\CategoryDestinationMediaPolicyPreferencePolicyInterface;
use App\ValueObject\CategoryDestinationMediaPolicyPreference;
use App\ValueObjectInterface\CategoryDestinationMediaPolicyPreferenceInterface;
/**
 * Provides the category destination media policy preference policy implementation.
 */
final class CategoryDestinationMediaPolicyPreferencePolicy
    implements CategoryDestinationMediaPolicyPreferencePolicyInterface
{
private const array MODES = ['strict_exact', 'allow_fallback', 'prefer_exact_warn'];
    /**
     * @param array<string,mixed> $strictPayload
     * @param array<string,mixed> $fallbackPayload
     */
    public function buildReport(
        string $mediaPolicyMode,
        array $strictPayload,
        array $fallbackPayload,
    ): CategoryDestinationMediaPolicyPreferenceInterface
    {
        $mode = trim($mediaPolicyMode);
        if (!in_array($mode, self::MODES, true)) {
            throw new \InvalidArgumentException('Unsupported destination media policy mode.');
        }

        $strictPublishable = (bool) ($strictPayload['publishable'] ?? false);
        $fallbackPublishable = (bool) ((
            $fallbackPayload['publishableWithFallback'] ?? false
        ) ?: (
            $fallbackPayload['publishable'] ?? false
        ));
        $strictMissing = $this->normalizeList($strictPayload['requiredMissing'] ?? null);
        $fallbackMissing = $this->normalizeList($fallbackPayload['requiredMissing'] ?? null);
        $warnings = $this->normalizeList(array_merge(
            $this->normalizeList($strictPayload['warnings'] ?? null),
            $this->normalizeList($fallbackPayload['warnings'] ?? null),
        ));
        $fallbackChecks = is_array($fallbackPayload['checks'] ?? null) ? $fallbackPayload['checks'] : [];
        $fallbackUsed = (bool) ($fallbackChecks['fallbackUsed'] ?? false);

        $checks = [
            'destinationMediaPolicyStrictExact' => 'strict_exact' === $mode,
            'destinationMediaPolicyAllowFallback' => 'allow_fallback' === $mode,
            'destinationMediaPolicyPreferExactWarn' => 'prefer_exact_warn' === $mode,
            'strictPublishable' => $strictPublishable,
            'fallbackPublishable' => $fallbackPublishable,
            'fallbackUsed' => $fallbackUsed,
            'resolvedPublishable' => false,
            'fallbackAcceptedByPolicy' => false,
        ];

        $requiredMissing = $strictMissing;
        $resolvedPublishable = $strictPublishable;

        if ('allow_fallback' === $mode) {
            $requiredMissing = $fallbackMissing;
            $resolvedPublishable = $fallbackPublishable;
            $checks['fallbackAcceptedByPolicy'] = $fallbackUsed && $fallbackPublishable;
        }

        if ('prefer_exact_warn' === $mode) {
            $requiredMissing = $fallbackMissing;
            $resolvedPublishable = $fallbackPublishable;
            $checks['fallbackAcceptedByPolicy'] = $fallbackUsed && $fallbackPublishable;
            if ($fallbackUsed && $fallbackPublishable && !$strictPublishable) {
                $warnings[] = 'destination_media_policy_preferred_exact_fallback_used';
            }
        }

        if ('strict_exact' === $mode && $fallbackUsed && !$strictPublishable && $fallbackPublishable) {
            $warnings[] = 'destination_media_policy_strict_exact_rejected_fallback';
        }

        $checks['resolvedPublishable'] = $resolvedPublishable;

        return new CategoryDestinationMediaPolicyPreference(
            $mode,
            $checks,
            array_values(array_unique($requiredMissing)),
            array_values(array_unique($warnings)),
            $strictPublishable,
            $fallbackPublishable,
            $resolvedPublishable,
            $fallbackUsed,
        );
    }

    /** @return list<string> */
    private function normalizeList(mixed $values): array
    {
        if (!is_array($values)) {
            return [];
        }

        $normalized = [];
        foreach ($values as $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $item = trim((string) $value);
            if ('' !== $item) {
                $normalized[] = $item;
            }
        }

        return array_values(array_unique($normalized));
    }
}
