<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

/**
 * Defines the contract for category sitemap generator.
 */
interface CategorySitemapGeneratorInterface
{
    /**
     * Handles the generate index workflow.
     */
    public function generateIndex(int $batchSize): string;
}
