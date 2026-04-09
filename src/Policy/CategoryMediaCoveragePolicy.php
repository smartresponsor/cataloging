<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Policy;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\PolicyInterface\CategoryMediaCoveragePolicyInterface;
use App\ValueObject\CategoryMediaCoverageReport;
use App\ValueObjectInterface\CategoryMediaCoverageReportInterface;

/**
 * Provides the category media coverage policy implementation.
 */
final class CategoryMediaCoveragePolicy implements CategoryMediaCoveragePolicyInterface
{
    /**
     * @param array<string,mixed>                      $payload
     * @param array<int,CategoryMediaBindingInterface> $bindings
     */
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

        $hasInlinePrimary = '' !== $this->scalarString($media['primaryAssetId'] ?? null);
        $hasInlineBanner = '' !== $this->scalarString($presentation['bannerId'] ?? null);
        $hasInlineManagedMedia = $hasInlinePrimary || $hasInlineBanner;
        $requiredMediaCoverageReady = [] !== $requiredBindings
            ? $this->allRequiredBindingsCovered($requiredBindings)
            : !$hasInlineManagedMedia;

        $checks = [
            'mediaReady' => $hasPrimaryBinding || $hasInlinePrimary,
            'bannerReady' => $hasBannerBinding || $hasInlineBanner,
            'heroReady' => $hasHeroBinding,
            'requiredMediaCoverageReady' => $requiredMediaCoverageReady,
        ];

        $requiredMissing = [];
        if (!$requiredMediaCoverageReady) {
            $requiredMissing[] = 'requiredMediaCoverageReady';
        }

        $warnings = [];
        foreach (['mediaReady', 'bannerReady', 'heroReady'] as $name) {
            if (true !== $checks[$name]) {
                $warnings[] = $name;
            }
        }

        return new CategoryMediaCoverageReport($checks, $requiredMissing, $warnings);
    }

    /** @param list<CategoryMediaBindingInterface> $bindings */
    private function hasRole(array $bindings, string $role): bool
    {
        return array_any($bindings, fn ($binding) => $binding->role()->value() === $role);
    }

    /** @param list<CategoryMediaBindingInterface> $bindings */
    private function allRequiredBindingsCovered(array $bindings): bool
    {
        if (array_any($bindings, fn ($binding) => '' === trim($binding->assetId()))) {
            return false;
        }

        return true;
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
