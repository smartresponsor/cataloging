<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategoryLinkInterface;
use Doctrine\ORM\Mapping as ORM;

/** Universal link of Category to any target domain entity. */
#[ORM\Entity]
#[ORM\Table(name: 'category_link')]
#[ORM\UniqueConstraint(name: 'uniq_category_link_target', columns: ['category_id', 'target_domain', 'target_class', 'target_id'])]
final class CategoryLink implements CategoryLinkInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(name: 'taxonomy_id', type: 'string', length: 26, options: ['fixed' => true])]
    private string $taxonomyId;

    #[ORM\Column(name: 'category_id', type: 'string', length: 26, options: ['fixed' => true])]
    private string $categoryId;

    #[ORM\Column(name: 'target_domain', type: 'string', length: 64)]
    private string $targetDomain;

    #[ORM\Column(name: 'target_class', type: 'string', length: 128)]
    private string $targetClass;

    #[ORM\Column(name: 'target_id', type: 'string', length: 64)]
    private string $targetId;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $taxonomyId,
        string $categoryId,
        string $targetDomain,
        string $targetClass,
        string $targetId,
        ?\DateTimeImmutable $createdAt = null,
    ) {
        $this->id = $id;
        $this->taxonomyId = $taxonomyId;
        $this->categoryId = $categoryId;
        $this->targetDomain = $targetDomain;
        $this->targetClass = $targetClass;
        $this->targetId = $targetId;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable('now');
    }

    public function id(): string
    {
        return $this->id;
    }

    public function taxonomyId(): string
    {
        return $this->taxonomyId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function targetDomain(): string
    {
        return $this->targetDomain;
    }

    public function targetClass(): string
    {
        return $this->targetClass;
    }

    public function targetId(): string
    {
        return $this->targetId;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
