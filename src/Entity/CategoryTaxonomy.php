<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategoryTaxonomyInterface;
use Doctrine\ORM\Mapping as ORM;

/** Category taxonomy entity (code: product, project, vendor, report, etc.). */
#[ORM\Entity]
#[ORM\Table(name: 'category_taxonomy')]
#[ORM\UniqueConstraint(name: 'uniq_category_taxonomy_code', columns: ['code'])]
final class CategoryTaxonomy implements CategoryTaxonomyInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    /** @var array<string,string> */
    #[ORM\Column(type: 'json')]
    private array $name;

    /** @var array<string,mixed> */
    #[ORM\Column(type: 'json')]
    private array $rule;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $code,
        array $name,
        array $rule,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
    ) {
        $now = new \DateTimeImmutable('now');
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->rule = $rule;
        $this->createdAt = $createdAt ?? $now;
        $this->updatedAt = $updatedAt ?? $now;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    /** @return array<string,string> */
    public function name(): array
    {
        return $this->name;
    }

    /** @return array<string,mixed> */
    public function rule(): array
    {
        return $this->rule;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
