<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishченко / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenко <dev@smartresponsor.com>
*/

namespace App\Layer\Category;

final class PublishEvent
{
    private string $categoryId;

    public function __construct(string $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }
}
