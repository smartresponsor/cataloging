<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

interface VersionInterface
{
    public function id(): string;

    public function categoryId(): string;

    public function number(): int;

    public function createdAt(): \DateTimeImmutable;
}
