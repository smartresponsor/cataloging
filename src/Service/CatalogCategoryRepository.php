<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Repository for tests — provides read/write with idempotency.
 */

namespace App\Service;

use SmartResponsor\tests\Layer\Domain\tests;

interface CatalogtestsRepository
{
    public function save(tests $category): void;

    public function getById(string $id): ?tests;

    public function getBySlug(string $slug): ?tests;

    public function move(string $id, ?string $newParentId): void;
}
