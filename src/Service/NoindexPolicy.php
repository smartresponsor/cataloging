<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

final class NoindexPolicy
{
    public function shouldNoindex(bool $virtual): bool
    {
        return $virtual;
    }
}
