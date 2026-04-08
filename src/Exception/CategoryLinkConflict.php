<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Exception;
/**
 * Provides the category link conflict implementation.
 */
final class CategoryLinkConflict extends \RuntimeException
{
    /**
     * Initializes the category link conflict service collaborators.
     */
    public function __construct(string $detail = '')
    {
        parent::__construct('Link already exists'.('' !== $detail ? ': '.$detail : ''));
    }
}
