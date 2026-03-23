<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category_alias')]
class CategoryAliasEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180)]
    private string $oldSlug;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $oldSlug, string $categoryId)
    {
        $this->oldSlug = $oldSlug;
        $this->categoryId = $categoryId;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function id(): int
    {
        return $this->id;
    }

    public function oldSlug(): string
    {
        return $this->oldSlug;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
