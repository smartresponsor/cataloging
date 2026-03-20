<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class SurrogateKeyHeader
{
    public function make(array $keys): array
    {
        return ['Surrogate-Key' => implode(' ', array_unique($keys))];
    }
}
