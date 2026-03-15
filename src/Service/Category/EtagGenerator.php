<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Category;

final class EtagGenerator
{
    public function forArray(array $data): string
    {
        ksort($data);

        return '"'.sha1(json_encode($data, JSON_UNESCAPED_SLASHES)).'"';
    }
}
