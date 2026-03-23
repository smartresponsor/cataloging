<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

interface CategoryPublicationReadinessInterface
{
    public function isPublishable(): bool;

    public function hasCheck(string $name): bool;

    public function check(string $name): bool;

    /** @return list<string> */
    public function blockers(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;
}
