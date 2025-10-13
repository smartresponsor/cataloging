<?php
declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/
namespace App\Layer\Category;

final class Version
{
    private string $id;
    private string $categoryId;
    private int $number;
    private \DateTimeImmutable $createdAt;

    public function __construct(string $id, string $categoryId, int $number, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->number = $number;
        $this->createdAt = $createdAt;
    }

    public function id(): string { return $this->id; }
    public function categoryId(): string { return $this->categoryId; }
    public function number(): int { return $this->number; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }
}
