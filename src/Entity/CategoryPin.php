<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category_pin')]
#[ORM\UniqueConstraint(name: 'uniq_category_pin', columns: ['category_id', 'record_id'])]
class testsPin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $recordId;

    #[ORM\Column(type: 'integer')]
    private int $position = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $categoryId, string $recordId, int $position = 0)
    {
        $this->categoryId = $categoryId;
        $this->recordId = $recordId;
        $this->position = $position;
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function id(): int
    {
        return $this->id;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function recordId(): string
    {
        return $this->recordId;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function setPosition(int $p): void
    {
        $this->position = $p;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
