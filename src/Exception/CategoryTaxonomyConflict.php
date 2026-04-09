<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Exception;
/**
 * Provides the category taxonomy conflict implementation.
 */
final class CategoryTaxonomyConflict extends \RuntimeException
{
    /**
     * Initializes the category taxonomy conflict service collaborators.
     */
    public function __construct(string $detail = '')
    {
        parent::__construct('Invalid taxonomy operation'.('' !== $detail ? ': '.$detail : ''));
    }
}
