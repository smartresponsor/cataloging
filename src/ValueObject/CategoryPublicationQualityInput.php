<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries score and check surfaces for publication quality evaluation.
 */
final readonly class CategoryPublicationQualityInput
{
    /**
     * @param array<string,bool> $publicationChecks
     * @param array<string,bool> $checks
     */
    public function __construct(
        private string $categoryId,
        private int $score,
        private array $publicationChecks,
        private array $checks,
    ) {
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function score(): int
    {
        return $this->score;
    }

    /** @return array<string,bool> */
    public function publicationChecks(): array
    {
        return $this->publicationChecks;
    }

    /** @return array<string,bool> */
    public function checks(): array
    {
        return $this->checks;
    }
}
