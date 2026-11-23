<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

interface CategorySitemapGeneratorInterface
{
    public function generateIndex(int $batchSize): string;
}
