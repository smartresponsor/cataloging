<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Policy;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\PolicyInterface\CategoryMediaCoveragePolicyInterface;
use App\ValueObject\CategoryMediaCoverageReport;
use App\ValueObjectInterface\CategoryMediaCoverageReportInterface;

final class CategoryMediaCoveragePolicy implements CategoryMediaCoveragePolicyInterface
{
    public function buildReport(array $payload, array $bindings): CategoryMediaCoverageReportInterface
    {
        $media = is_array($payload['media'] ?? null) ? $payload['media'] : [];
        $presentation = is_array($payload['presentation'] ?? null) ? $payload['presentation'] : [];

        $activeBindings = array_values(array_filter(
            $bindings,
            static fn (CategoryMediaBindingInterface $binding): bool => $binding->active(),
        ));

        $hasPrimaryBinding = $this->hasRole($activeBindings, 'primary');
        $hasBannerBinding = $this->hasRole($activeBindings, 'banner');
        $hasHeroBinding = $this->hasRole($activeBindings, 'hero');
        $requiredBindings = array_values(array_filter(
            $activeBindings,
            static fn (CategoryMediaBindingInterface $binding): bool => $binding->requiredForPublish(),
        ));

        $hasInlinePrimary = '' !== trim((string) ($media['primaryAssetId'] ?? ''));
        $hasInlineBanner = '' !== trim((string) ($presentation['bannerId'] ?? ''));
        $hasInlineManagedMedia = $hasInlinePrimary || $hasInlineBanner;

        $checks = [
            'mediaReady' => $hasPrimaryBinding || $hasInlinePrimary,
            'bannerReady' => $hasBannerBinding || $hasInlineBanner,
            'heroReady' => $hasHeroBinding,
            'requiredMediaCoverageReady' => [] !== $requiredBindings
                ? $this->allRequiredBindingsCovered($requiredBindings)
                : !$hasInlineManagedMedia,
        ];

        $requiredMissing = [];
        if (($checks['requiredMediaCoverageReady'] ?? false) !== true) {
            $requiredMissing[] = 'requiredMediaCoverageReady';
        }

        $warnings = [];
        foreach (['mediaReady', 'bannerReady', 'heroReady'] as $name) {
            if (($checks[$name] ?? false) !== true) {
                $warnings[] = $name;
            }
        }

        return new CategoryMediaCoverageReport($checks, $requiredMissing, $warnings);
    }

    /** @param list<CategoryMediaBindingInterface> $bindings */
    private function hasRole(array $bindings, string $role): bool
    {
        foreach ($bindings as $binding) {
            if ($binding->role()->value() === $role) {
                return true;
            }
        }

        return false;
    }

    /** @param list<CategoryMediaBindingInterface> $bindings */
    private function allRequiredBindingsCovered(array $bindings): bool
    {
        foreach ($bindings as $binding) {
            if ('' === trim($binding->assetId())) {
                return false;
            }
        }

        return true;
    }
}
