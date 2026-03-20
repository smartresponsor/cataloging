<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\ServiceInterface;

interface ImporterInterface
{
    public function importCsv(string $path): int;

    public function importJson(string $path): int;
}
