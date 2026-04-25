<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface;

/**
 * Defines the contract for category linked.
 */
interface CatalogCategoryLinkEntityedInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;
}
if (!class_exists(__NAMESPACE__.'\\CategoryLinkedInterface', false)) {
    class_alias(CatalogCategoryLinkEntityedInterface::class, __NAMESPACE__.'\\CategoryLinkedInterface');
}
