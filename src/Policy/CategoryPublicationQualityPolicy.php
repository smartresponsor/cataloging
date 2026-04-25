<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\PolicyInterface\CategoryPublicationQualityPolicyInterface;
use App\Cataloging\ValueObject\CategoryPublicationQualityProfile;
use App\Cataloging\ValueObjectInterface\CategoryPublicationQualityProfileInterface;

/**
 * Provides the category publication quality policy implementation.
 */
final class CategoryPublicationQualityPolicy implements CategoryPublicationQualityPolicyInterface
{
    /**
     * Builds the profile result for the current workflow.
     */
    public function buildProfile(
        int $score,
        array $publicationChecks,
        array $checks,
    ): CategoryPublicationQualityProfileInterface {
        $normalizedPublicationChecks = [];
        foreach ($publicationChecks as $name => $value) {
            $normalizedPublicationChecks[(string) $name] = (bool) $value;
        }

        $normalizedChecks = [];
        foreach ($checks as $name => $value) {
            $normalizedChecks[(string) $name] = (bool) $value;
        }

        $hardBlockers = [];
        foreach (['slugReady', 'seoReady', 'contentReady', 'localeReady', 'requiredMediaCoverageReady'] as $name) {
            if (($normalizedPublicationChecks[$name] ?? true) !== true) {
                $hardBlockers[] = $name;
            }
        }

        if ($score < 50) {
            $hardBlockers[] = 'qualityScoreCritical';
        }

        $softWarnings = [];
        foreach (['mediaReady', 'slugHistoryReady'] as $name) {
            if (($normalizedPublicationChecks[$name] ?? true) !== true) {
                $softWarnings[] = $name;
            }
        }

        if ($score < 80) {
            $softWarnings[] = 'qualityScoreBelowTarget';
        }

        $advisoryWarnings = [];
        foreach (['bannerReady', 'htmlBlockReady', 'heroReady'] as $name) {
            if (($normalizedChecks[$name] ?? true) !== true) {
                $advisoryWarnings[] = $name;
            }
        }

        $riskLevel = 'ready';
        if ([] !== $hardBlockers) {
            $riskLevel = 'critical';
        } elseif ([] !== $softWarnings || [] !== $advisoryWarnings) {
            $riskLevel = 'attention';
        }

        return new CategoryPublicationQualityProfile(
            $score,
            array_values(array_unique($hardBlockers)),
            array_values(array_unique($softWarnings)),
            array_values(array_unique($advisoryWarnings)),
            $normalizedPublicationChecks,
            $normalizedChecks,
            $riskLevel,
        );
    }
}
