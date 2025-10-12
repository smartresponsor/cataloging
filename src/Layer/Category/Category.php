<?php
declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/
namespace App\Layer\Category;

final class Category
{
    private string $id;
    private ?string $parentId;
    private string $slug;
    private string $name;
    private string $locale;
    private string $status; // draft|published

    public function __construct(string $id, ?string $parentId, string $slug, string $name, string $locale, string $status)
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->slug = $slug;
        $this->name = $name;
        $this->locale = $locale;
        $this->status = $status;
    }

    public function id(): string { return $this->id; }
    public function parentId(): ?string { return $this->parentId; }
    public function slug(): string { return $this->slug; }
    public function name(): string { return $this->name; }
    public function locale(): string { return $this->locale; }
    public function status(): string { return $this->status; }
}
