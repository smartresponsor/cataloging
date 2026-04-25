<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Exception;

/**
 * Provides the category link conflict implementation.
 */
final class CatalogCategoryLinkEntityConflict extends \RuntimeException
{
    /**
     * Initializes the category link conflict service collaborators.
     */
    public function __construct(string $detail = '')
    {
        parent::__construct('Link already exists'.('' !== $detail ? ': '.$detail : ''));
    }
}
if (!class_exists(__NAMESPACE__.'\\CategoryLinkConflict', false)) {
    class_alias(CatalogCategoryLinkEntityConflict::class, __NAMESPACE__.'\\CategoryLinkConflict');
}
