<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'virtual_category')]
class VirtualCategoryEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26)]
    private string $id;

    #[ORM\Column(type: 'string', length: 160)]
    private string $name;

    /** @var array<string,mixed> */
    #[ORM\Column(type: 'json')]
    private array $rule;

    /** @param array<string,mixed> $rule */
    public function __construct(string $id, string $name, array $rule)
    {
        $this->id = $id;
        $this->name = $name;
        $this->rule = $rule;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return array<string,mixed> */
    public function getRule(): array
    {
        return $this->rule;
    }

    /** @param array<string,mixed> $rule */
    public function setRule(array $rule): void
    {
        $this->rule = $rule;
    }
}
