<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class VersionCompare
{
    public function diff(array $a, array $b): array
    {
        // Minimal deterministic diff (keys only); real compare can be extended later.
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
