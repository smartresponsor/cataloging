<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

namespace App\ServiceInterface;

interface VersionInterface
{
    public function id(): string;

    public function categoryId(): string;

    public function number(): int;

    public function createdAt(): \DateTimeImmutable;
}
