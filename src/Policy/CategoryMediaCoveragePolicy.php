<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Policy;

use App\Cataloging\EntityInterface\CatalogCategoryMediaBindingEntityInterface;
use App\Cataloging\PolicyInterface\CategoryMediaCoveragePolicyInterface;
use App\Cataloging\ValueObject\CategoryMediaCoverageReport;
use App\Cataloging\ValueObjectInterface\CategoryMediaCoverageReportInterface;

/**
 * Provides the category media coverage policy implementation.
 */
final class CategoryMediaCoveragePolicy implements CategoryMediaCoveragePolicyInterface
{
    /**
     * @param array<string,mixed>                                   $payload
     * @param array<int,CatalogCategoryMediaBindingEntityInterface> $bindings
     */
    public function buildReport(array $payload, array $bindings): CategoryMediaCoverageReportInterface
    {
        $media = is_array($payload['media'] ?? null) ? $payload['media'] : [];
        $presentation = is_array($payload['presentation'] ?? null) ? $payload['presentation'] : [];

        $activeBindings = array_values(array_filter(
            $bindings,
            static fn (CatalogCategoryMediaBindingEntityInterface $binding): bool => $binding->active(),
        ));

        $hasPrimaryBinding = $this->hasRole($activeBindings, 'primary');
        $hasBannerBinding = $this->hasRole($activeBindings, 'banner');
        $hasHeroBinding = $this->hasRole($activeBindings, 'hero');
        $requiredBindings = array_values(array_filter(
            $activeBindings,
            static fn (CatalogCategoryMediaBindingEntityInterface $binding): bool => $binding->requiredForPublish(),
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

    /** @param list<CatalogCategoryMediaBindingEntityInterface> $bindings */
    private function hasRole(array $bindings, string $role): bool
    {
        return array_any($bindings, fn ($binding) => $binding->role()->value() === $role);
    }

    /** @param list<CatalogCategoryMediaBindingEntityInterface> $bindings */
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
