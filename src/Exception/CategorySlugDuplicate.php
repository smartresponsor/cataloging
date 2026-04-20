<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Exception;

/**
 * Provides the category slug duplicate implementation.
 */
final class CategorySlugDuplicate extends \RuntimeException
{
    /**
     * Initializes the category slug duplicate service collaborators.
     */
    public function __construct(string $detail = '')
    {
        parent::__construct('Duplicate slug in taxonomy'.('' !== $detail ? ': '.$detail : ''));
    }
}
