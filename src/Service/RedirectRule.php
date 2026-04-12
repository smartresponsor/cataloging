<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the redirect rule application service.
 */
final class RedirectRule
{
    private string $from;
    private string $to;
    private int $status;

    /**
     * Initializes the redirect rule service collaborators.
     */
    public function __construct(string $from, string $to, int $status = 301)
    {
        $this->from = $from;
        $this->to = $to;
        $this->status = $status;
    }

    /**
     * Handles the from workflow.
     */
    public function from(): string
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
     * Handles the status workflow.
     */
    public function status(): int
    {
        return $this->status;
    }
}
