<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Exception;

/**
 * Provides the category taxonomy conflict implementation.
 */
final class CatalogTaxonomyConflict extends \RuntimeException
{
    /**
     * Initializes the category taxonomy conflict service collaborators.
     */
    public function __construct(string $detail = '')
    {
        parent::__construct('Invalid taxonomy operation'.('' !== $detail ? ': '.$detail : ''));
    }
}
if (!class_exists(__NAMESPACE__.'\\TaxonomyConflict', false)) {
    class_alias(CatalogTaxonomyConflict::class, __NAMESPACE__.'\\TaxonomyConflict');
}
