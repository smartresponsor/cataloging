<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Service-level category contract.
 */

namespace App\Service\Category;

interface CategoryInterface
{
    public function id(): string;

    public function slug(): string;

    public function title(): string;

    public function parentId(): ?string;

    public function path(): string;

    public function depth(): int;

    public function createdAt(): \DateTimeImmutable;

    public function updatedAt(): \DateTimeImmutable;
}
