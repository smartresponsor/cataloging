<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class VersionCompare
{
    /**
     * @param array<string, scalar|null> $a
     * @param array<string, scalar|null> $b
     *
     * @return array<string, array{from: scalar|null, to: scalar|null}>
     */
    public function diff(array $a, array $b): array
    {
        $result = [];
        foreach ($a as $k => $v) {
            if (!array_key_exists($k, $b) || $b[$k] !== $v) {
                $result[$k] = ['from' => $v, 'to' => $b[$k] ?? null];
            }
        }
        foreach ($b as $k => $v) {
            if (!array_key_exists($k, $a)) {
                $result[$k] = ['from' => null, 'to' => $v];
            }
        }

        return $result;
    }
}
