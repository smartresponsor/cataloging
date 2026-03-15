<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Entity\Category;

final class Category
{
    public string $id;
    public string $name;
    public string $slug;
    public ?string $iconUrl = null;
}
