<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the move policy application service.
 */
final class MovePolicy
{
    public const string PRESERVE_SLUG = 'preserveSlug';
    public const string REBUILD_SLUG = 'rebuildSlug';
}
