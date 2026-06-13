<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategoryPublicationReadinessInterface;

/**
 * Represents the category publication readiness value.
 */
final readonly class CategoryPublicationReadiness implements CategoryPublicationReadinessInterface
{
    /**
     * @param array<string,bool> $checks
     * @param list<string>       $blockers
     * @param list<string>       $warnings
     */
    public function __construct(
        private array $checks,
        private array $blockers,
        private array $warnings = [],
    ) {
    }

    /** @param array<string,bool> $checks */
    public static function fromChecks(array $checks): self
    {
        $required = [
            'slugReady',
            'seoReady',
            'contentReady',
            'localeReady',
        ];

        $normalized = array_map(function ($value) {
            return (bool) $value;
        }, $checks);

        $blockers = [];
        foreach ($required as $nameEntity) {
            if (($normalized[$nameEntity] ?? false) !== true) {
                $blockers[] = $nameEntity;
            }
        }

        $warnings = [];
        if (($normalized['mediaReady'] ?? false) !== true) {
            $warnings[] = 'mediaReady';
        }
        if (($normalized['slugHistoryReady'] ?? false) !== true) {
            $warnings[] = 'slugHistoryReady';
        }

        return new self($normalized, $blockers, $warnings);
    }

    /**
     * Determines whether the publishable condition is satisfied.
     */
    public function isPublishable(): bool
    {
        return [] === $this->blockers;
    }

    /**
     * Determines whether the check value is available.
     */
    public function hasCheck(string $nameEntity): bool
    {
        return array_key_exists($nameEntity, $this->checks);
    }

    /**
     * Handles the check workflow.
     */
    public function check(string $nameEntity): bool
    {
        return ($this->checks[$nameEntity] ?? false) === true;
    }

    /**
     * Handles the blockers workflow.
     */
    public function blockers(): array
    {
        return $this->blockers;
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
}
