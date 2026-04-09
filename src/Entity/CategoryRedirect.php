<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

/** Redirect record from old full slug to new one (SEO). */
final class CategoryRedirect
{
    private string $id;
    private string $from;
    private string $to;
    private \DateTimeImmutable $createdAt;

    /**
     * Initializes the category redirect service collaborators.
     */
    public function __construct(string $id, string $from, string $to, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
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
     * Handles the frm workflow.
     */
    public function frm(): string
    {
        return $this->from;
    }

    /**
     * Handles the to workflow.
     */
    public function to(): string
    {
        return $this->to;
    }

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
