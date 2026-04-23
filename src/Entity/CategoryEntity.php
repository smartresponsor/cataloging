<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * Represents the category entity domain record.
 */
#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[ORM\Index(name: 'idx_category_path', columns: ['path'])]
/** @noinspection PhpPropertyNamingConventionInspection */
class CategoryEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(type: 'string', length: 160)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $slug;

    // Stored in Postgres as ltree via a custom Doctrine type.
    #[ORM\Column(type: 'ltree')]
    private string $path;

    #[ORM\Column(type: 'integer')]
    private int $depth;

    /**
     * Initializes the category entity service collaborators.
     */
    public function __construct(string $name, string $slug, string $path, int $depth)
    {
        $this->id = (string) new Ulid();
        $this->name = $name;
        $this->slug = $slug;
        $this->path = $path;
        $this->depth = $depth;
    }

    /**
     * Returns the id value.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the name value.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Updates the name value.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the slug value.
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Updates the slug value.
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Returns the path value.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Updates the path value.
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Returns the depth value.
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * Updates the depth value.
     */
    public function setDepth(int $depth): void
    {
        $this->depth = $depth;
    }

    /**
     * Returns the parent path value.
     */
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

    /**
     * Determines whether the direct child of condition is satisfied.
     */
    public function isDirectChildOf(self $parent): bool
    {
        if ($this->depth !== $parent->depth + 1) {
            return false;
        }

        return $this->getParentPath() === $parent->path;
    }
}
