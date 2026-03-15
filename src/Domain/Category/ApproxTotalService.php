<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 */

namespace App\Service\Category\Domain;

final class ApproxTotalService
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /** @return array{value:int,accuracy:string} */
    public function get(string $key, bool $withTotal): array
    {
        if ($withTotal) {
            return ['value' => 0, 'accuracy' => 'exact'];
        } // hook for exact path
        if (!is_file($this->file)) {
            return ['value' => 0, 'accuracy' => 'approx'];
        }
        $data = json_decode((string) file_get_contents($this->file), true) ?: [];

        return ['value' => (int) ($data[$key] ?? 0), 'accuracy' => 'approx'];
    }
}
