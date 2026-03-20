<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Entity;

final class Category
{
    public string $id;
    public string $name;
    public string $slug;
    public ?string $iconUrl = null;
}
