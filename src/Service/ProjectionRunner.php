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
        $output = [];
        foreach ($nodes as $node) {
            $node['path'] = $node['path'] ?? '/'.$this->stringValue($node, 'id');
            $output[] = $node;
        }

        return $output;
    }

    /**
     * @param array<string,mixed> $node
     *
     * @noinspection PhpSameParameterValueInspection
     */
    private function stringValue(array $node, string $key): string
    {
        $value = $node[$key] ?? '';

        return is_scalar($value) ? (string) $value : '';
    }
}
