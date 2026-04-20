<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

/*
Copyright (c) 2025 Oleksandr Tishченко / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenко <dev@highhopesamerica.com>
*/

namespace App\Cataloging\Service;

/**
 * Provides the publish event application service.
 */
final class PublishEvent
{
    private string $categoryId;

    /**
     * Initializes the publish event service collaborators.
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
