<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\ServiceInterface;

interface RedirectStoreInterface
{
    public function put(string $from, string $to, int $status = 301): void;

    public function get(string $from): ?array;
}
