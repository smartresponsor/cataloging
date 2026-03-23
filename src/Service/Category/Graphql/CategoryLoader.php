<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Graphql;

final class CategoryLoader
{
    /** @var callable(string[]): array<int, array{id: string, name: string, slug: string}> */
    private $batch;

    public function __construct(callable $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Batch load by IDs.
     *
     * @param string[] $ids
     *
     * @return array<int, array{id: string, name: string, slug: string}>
     */
    public function load(array $ids): array
    {
        return ($this->batch)($ids);
    }
}
