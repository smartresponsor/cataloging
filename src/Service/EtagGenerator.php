<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class EtagGenerator
{
    /** @param array<string|int,mixed> $data */
    public function forArray(array $data): string
    {
        ksort($data);
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        return '"'.sha1($json).'"';
    }
}
