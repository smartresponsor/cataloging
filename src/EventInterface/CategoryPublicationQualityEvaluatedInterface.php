<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\EventInterface;

interface CategoryPublicationQualityEvaluatedInterface
{
    public function payload(): array;
}
