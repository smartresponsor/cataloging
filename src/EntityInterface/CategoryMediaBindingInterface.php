<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\EntityInterface;

use App\ValueObjectInterface\CategoryMediaRoleInterface;

interface CategoryMediaBindingInterface
{
    public function bindingId(): string;

    public function categoryId(): string;

    public function assetId(): string;

    public function role(): CategoryMediaRoleInterface;

    /** @return list<string> */
    public function channels(): array;

    /** @return list<string> */
    public function locales(): array;

    public function requiredForPublish(): bool;

    public function active(): bool;

    /** @return array<string,mixed> */
    public function metadata(): array;

    public function actorId(): string;

    public function boundAt(): \DateTimeImmutable;
}
