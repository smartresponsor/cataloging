<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;
/**
 * Represents the category domain record.
 */
final class Category
{
    public string $id;
    public string $name;
    public string $slug;
    public ?string $iconUrl = null;
}
