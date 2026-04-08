<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the projection runner application service.
 */
final class ProjectionRunner
{
    /**
     * @param list<array<string,mixed>> $nodes
     *
     * @return list<array<string,mixed>>
     */
    public function run(array $nodes): array
    {
        $out = [];
        foreach ($nodes as $n) {
            $n['path'] = $n['path'] ?? '/'.$this->stringValue($n, 'id');
            $out[] = $n;
        }

        return $out;
    }

    /** @param array<string,mixed> $node */
    private function stringValue(array $node, string $key): string
    {
        $value = $node[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }
}
