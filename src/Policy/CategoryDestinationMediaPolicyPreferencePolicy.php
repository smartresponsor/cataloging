<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategoryDestinationMediaPolicyPreferencePolicyInterface;
use App\Cataloging\Service\CategoryMediaInputNormalizer;
use App\Cataloging\ValueObject\CategoryDestinationMediaPolicyPreference;
use App\Cataloging\ValueObjectInterface\CategoryDestinationMediaPolicyPreferenceInterface;

/**
 * Provides the category destination media policy preference policy implementation.
 */
final class CategoryDestinationMediaPolicyPreferencePolicy implements CategoryDestinationMediaPolicyPreferencePolicyInterface
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
    ): CategoryDestinationMediaPolicyPreferenceInterface {
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
        $strictMissing = CategoryMediaInputNormalizer::stringList($strictPayload['requiredMissing'] ?? null);
        $fallbackMissing = CategoryMediaInputNormalizer::stringList($fallbackPayload['requiredMissing'] ?? null);
        $warnings = CategoryMediaInputNormalizer::stringList(array_merge(
            CategoryMediaInputNormalizer::stringList($strictPayload['warnings'] ?? null),
            CategoryMediaInputNormalizer::stringList($fallbackPayload['warnings'] ?? null),
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

        $checks['fallbackAcceptedByPolicy'] = $checks['fallbackAcceptedByPolicy'] ?? false;
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
}
