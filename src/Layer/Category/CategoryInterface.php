<?php
declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/
namespace App\Layer\Category;

interface CategoryInterface
{
    public function id(): string;
    public function parentId(): ?string;
    public function slug(): string;
    public function name(): string;
    public function locale(): string;
    public function status(): string;
}
