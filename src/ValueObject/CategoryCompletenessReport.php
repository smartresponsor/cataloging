<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategoryCompletenessReportInterface;

/**
 * Represents the category completeness report value.
 */
final readonly class CategoryCompletenessReport implements CategoryCompletenessReportInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $missingRequired
     * @param list<string>       $warnings
     */
    public function __construct(
        private array $checks,
        private array $missingRequired,
        private array $warnings,
        private int $score,
    ) {
    }

    /** @param array<string,bool> $checks */
    public static function fromChecks(array $checks): self
    {
        $normalized = array_map(function ($value) {
            return (bool) $value;
        }, $checks);

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
        foreach (['mediaReady', 'slugHistoryReady', 'bannerReady', 'htmlBlockReady'] as $name) {
            if (($normalized[$name] ?? false) !== true) {
                $warnings[] = $name;
            }
        }

        $total = count($normalized);
        $passed = count(array_filter($normalized, static fn (bool $value): bool => $value));
        $score = 0 === $total ? 0 : (int) floor(($passed / $total) * 100);

        return new self($normalized, $missingRequired, $warnings, $score);
    }

    /**
     * Handles the score workflow.
     */
    public function score(): int
    {
        return $this->score;
    }

    /**
     * Determines whether the complete condition is satisfied.
     */
    public function isComplete(): bool
    {
        return [] === $this->missingRequired;
    }

    /**
     * Handles the missing required workflow.
     */
    public function missingRequired(): array
    {
        return $this->missingRequired;
    }

    /**
     * Handles the warnings workflow.
     */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /**
     * Handles the checks workflow.
     */
    public function checks(): array
    {
        return $this->checks;
    }

    /**
     * Handles the publication checks workflow.
     */
    public function publicationChecks(): array
    {
        return [
            'slugReady' => ($this->checks['slugReady'] ?? false) === true,
            'seoReady' => ($this->checks['seoTitleReady'] ?? false) === true
                && ($this->checks['seoDescriptionReady'] ?? false) === true,
            'contentReady' => ($this->checks['contentReady'] ?? false) === true,
            'localeReady' => ($this->checks['localeCoverageReady'] ?? false) === true,
            'mediaReady' => ($this->checks['mediaReady'] ?? false) === true,
            'slugHistoryReady' => ($this->checks['slugHistoryReady'] ?? false) === true,
            'requiredMediaCoverageReady' => ($this->checks['requiredMediaCoverageReady'] ?? true) === true,
        ];
    }
}
