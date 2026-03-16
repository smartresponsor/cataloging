<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EntityInterface;

interface CategoryLinkInterface
{
    public function id(): string;

    public function taxonomyId(): string;

    public function categoryId(): string;

    public function targetDomain(): string;

    public function targetClass(): string;

    public function targetId(): string;

    public function createdAt(): \DateTimeImmutable;
}
