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
        foreach ($publicationChecks as $nameEntity => $value) {
            $normalizedPublicationChecks[(string) $nameEntity] = (bool) $value;
        }

        $normalizedChecks = [];
        foreach ($checks as $nameEntity => $value) {
            $normalizedChecks[(string) $nameEntity] = (bool) $value;
        }

        $hardBlockers = [];
        foreach (['slugReady', 'seoReady', 'contentReady', 'localeReady', 'requiredMediaCoverageReady'] as $nameEntity) {
            if (($normalizedPublicationChecks[$nameEntity] ?? true) !== true) {
                $hardBlockers[] = $nameEntity;
            }
        }

        if ($score < 50) {
            $hardBlockers[] = 'qualityScoreCritical';
        }

        $softWarnings = [];
        foreach (['mediaReady', 'slugHistoryReady'] as $nameEntity) {
            if (($normalizedPublicationChecks[$nameEntity] ?? true) !== true) {
                $softWarnings[] = $nameEntity;
            }
        }

        if ($score < 80) {
            $softWarnings[] = 'qualityScoreBelowTarget';
        }

        $advisoryWarnings = [];
        foreach (['bannerReady', 'htmlBlockReady', 'heroReady'] as $nameEntity) {
            if (($normalizedChecks[$nameEntity] ?? true) !== true) {
                $advisoryWarnings[] = $nameEntity;
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
