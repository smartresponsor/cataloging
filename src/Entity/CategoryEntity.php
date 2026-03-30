<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[ORM\Index(columns: ['path'], name: 'idx_category_path')]
class CategoryEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(type: 'string', length: 160)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $slug;

    // Stored in Postgres as ltree; mapped as string (mapping_types in doctrine.yaml).
    #[ORM\Column(type: 'string', length: 2048, options: ['comment' => 'ltree'], columnDefinition: 'ltree')]
    private string $path;

    #[ORM\Column(type: 'integer')]
    private int $depth;

    public function __construct(string $name, string $slug, string $path, int $depth)
    {
        $this->id = (new Ulid())->__toString();
        $this->name = $name;
        $this->slug = $slug;
        $this->path = $path;
        $this->depth = $depth;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function setDepth(int $depth): void
    {
        $this->depth = $depth;
    }

    public function getParentPath(): ?string
    {
        if ($this->depth <= 0) {
            return null;
        }

        $separatorPosition = strrpos($this->path, '.');
        if (false === $separatorPosition) {
            return null;
        }

        return substr($this->path, 0, $separatorPosition);
    }

    public function isDirectChildOf(self $parent): bool
    {
        if ($this->depth !== $parent->depth + 1) {
            return false;
        }

        return $this->getParentPath() === $parent->path;
    }
}
