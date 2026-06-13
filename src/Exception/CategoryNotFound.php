<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Exception;

/**
 * Provides the category not found implementation.
 */
final class CategoryNotFound extends \RuntimeException
{
    /**
     * Initializes the category not found service collaborators.
     */
    public function __construct(string $detail = '')
    {
        parent::__construct('CategoryEntity not found'.('' !== $detail ? ': '.$detail : ''));
    }
}
