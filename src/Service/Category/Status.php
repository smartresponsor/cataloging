<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Category;

final class Status
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::DRAFT, self::PUBLISHED], true)) {
            throw new \InvalidArgumentException('Invalid status');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isDraft(): bool
    {
        return self::DRAFT === $this->value;
    }

    public function isPublished(): bool
    {
        return self::PUBLISHED === $this->value;
    }
}
