<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

/**
 * Defines the contract for category.
 */
interface CategoryInterface
{
    /**
     * Handles the id workflow.
     */
    public function id(): string;

    /**
     * Handles the taxonomy id workflow.
     */
    public function taxonomyId(): string;

    /**
     * Handles the parent id workflow.
     */
    public function parentId(): ?string;

    /** @return array<string,string> */
    public function name(): array;

    /** @return array<string,string> */
    public function slug(): array;

    /**
     * Handles the path workflow.
     */
    public function path(): string;

    /**
     * Handles the order workflow.
     */
    public function order(): int;

    /** @return array<string,mixed> */
    public function meta(): array;

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable;

    /**
     * Handles the updated at workflow.
     */
    public function updatedAt(): \DateTimeImmutable;
}
