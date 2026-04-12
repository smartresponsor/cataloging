<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

/*
Copyright (c) 2025 Oleksandr Tishченко / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenко <dev@highhopesamerica.com>
*/

namespace App\Service;

/**
 * Provides the move event application service.
 */
final class MoveEvent
{
    private string $categoryId;

    /**
     * Initializes the move event service collaborators.
     */
    public function __construct(string $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }
}
