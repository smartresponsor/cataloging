<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\VersionInterface;

/**
 * Provides the version application service.
 */
final class Version implements VersionInterface
{
    private string $id;
    private string $categoryId;
    private int $number;
    private \DateTimeImmutable $createdAt;

    /**
     * Initializes the version service collaborators.
     */
    public function __construct(string $id, string $categoryId, int $number, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->number = $number;
        $this->createdAt = $createdAt;
    }

    /**
     * Handles the id workflow.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the number workflow.
     */
    public function number(): int
    {
        return $this->number;
    }

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
