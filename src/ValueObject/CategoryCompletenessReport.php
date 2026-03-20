<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryCompletenessReportInterface;

final class CategoryCompletenessReport implements CategoryCompletenessReportInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $missingRequired
     * @param list<string>       $warnings
     */
    public function __construct(
        private readonly array $checks,
        private readonly array $missingRequired,
        private readonly array $warnings,
        private readonly int $score,
    ) {
    }

    /** @param array<string,bool> $checks */
    public static function fromChecks(array $checks): self
    {
        $normalized = [];
        foreach ($checks as $name => $value) {
            $normalized[(string) $name] = (bool) $value;
        }

        $required = [
            'slugReady',
            'seoTitleReady',
            'seoDescriptionReady',
            'contentReady',
            'localeCoverageReady',
        ];

        $missingRequired = [];
        foreach ($required as $name) {
            if (($normalized[$name] ?? false) !== true) {
                $missingRequired[] = $name;
            }
        }

        $warnings = [];
        foreach (['mediaReady', 'aliasReady', 'bannerReady', 'htmlBlockReady'] as $name) {
            if (($normalized[$name] ?? false) !== true) {
                $warnings[] = $name;
            }
        }

        $total = count($normalized);
        $passed = count(array_filter($normalized, static fn (bool $value): bool => $value));
        $score = 0 === $total ? 0 : (int) floor(($passed / $total) * 100);

        return new self($normalized, $missingRequired, $warnings, $score);
    }

    public function score(): int
    {
        return $this->score;
    }

    public function isComplete(): bool
    {
        return [] === $this->missingRequired;
    }

    public function missingRequired(): array
    {
        return $this->missingRequired;
    }

    public function warnings(): array
    {
        return $this->warnings;
    }

    public function checks(): array
    {
        return $this->checks;
    }

    public function publicationChecks(): array
    {
        return [
            'slugReady' => ($this->checks['slugReady'] ?? false) === true,
            'seoReady' => ($this->checks['seoTitleReady'] ?? false) === true && ($this->checks['seoDescriptionReady'] ?? false) === true,
            'contentReady' => ($this->checks['contentReady'] ?? false) === true,
            'localeReady' => ($this->checks['localeCoverageReady'] ?? false) === true,
            'mediaReady' => ($this->checks['mediaReady'] ?? false) === true,
            'aliasReady' => ($this->checks['aliasReady'] ?? false) === true,
            'requiredMediaCoverageReady' => ($this->checks['requiredMediaCoverageReady'] ?? true) === true,
        ];
    }
}
