<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategoryDestinationMediaReadinessPolicyInterface;
use App\Cataloging\Service\CategoryMediaInputNormalizer;
use App\Cataloging\ValueObject\CategoryDestinationMediaReadinessContext;
use App\Cataloging\ValueObject\CategoryDestinationMediaReadinessReport;
use App\Cataloging\ValueObject\CategoryDestinationMediaReadinessState;
use App\Cataloging\ValueObjectInterface\CategoryDestinationMediaReadinessReportInterface;

/**
 * Provides the category destination media readiness policy implementation.
 */
/** @noinspection PhpUnusedLocalVariableInspection */
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

        $channel = CategoryMediaInputNormalizer::stringValue($destinationSettings['channel'] ?? null);
        $locale = CategoryMediaInputNormalizer::stringValue($destinationSettings['locale'] ?? null);
        $requiredRoles = CategoryMediaInputNormalizer::stringList($destinationSettings['requiredMediaRoles'] ?? null);

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

        $requiredMissing = array_unique($requiredMissing);
        $warnings = array_unique($warnings);
        $normalizedMatchedBindingIds = [];
        foreach ($matchedBindingIds as $matchedBindingId) {
            $trimmedMatchedBindingId = trim($matchedBindingId);
            if ('' === $trimmedMatchedBindingId) {
                continue;
            }

            $normalizedMatchedBindingIds[] = $trimmedMatchedBindingId;
        }
        $matchedBindingIds = array_values(array_unique($normalizedMatchedBindingIds));
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
}
