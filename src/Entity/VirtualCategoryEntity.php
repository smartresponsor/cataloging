<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'virtual_category')]
final class VirtualCategoryEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26)]
    private string $id;

    #[ORM\Column(type: 'string', length: 160)]
    private string $name;

    #[ORM\Column(type: 'json')]
    private array $rule;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $id, string $name, array $rule, ?\DateTimeImmutable $createdAt = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->rule = $rule;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRule(): array
    {
        return $this->rule;
    }

    public function setRule(array $rule): void
    {
        $this->rule = $rule;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
