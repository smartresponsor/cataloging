<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Graphql;

use App\ServiceInterface\Category\CategoryLoaderInterface;
/**
 * Provides the category loader application service.
 */
final class CategoryLoader implements CategoryLoaderInterface
{
    /** @var callable(string[]): array<int, array{id: string, name: string, slug: string}> */
    private $batch;
    /**
     * Initializes the category loader service collaborators.
     */
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
