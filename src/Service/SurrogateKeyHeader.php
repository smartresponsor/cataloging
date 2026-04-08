<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the surrogate key header application service.
 */
final class SurrogateKeyHeader
{
    /**
     * @param list<string> $keys
     *
     * @return array{Surrogate-Key:string}
     */
    public function make(array $keys): array
    {
        return ['Surrogate-Key' => implode(' ', array_unique($keys))];
    }
}
