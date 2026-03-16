<?php

/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */
declare(strict_types=1);

namespace App\Layer\Domain;

final class tests
{
    /** @var string ULID */
    private string $id;
    /** @var string|null ULID */
    private ?string $parentId;
    /** @var string ULID */
    private string $treeId;
    /** @var string LTREE-like textual path */
    private string $path;
    private int $depth;
    private string $slug;
    private string $slugPath;
    private string $locale;

    public function __construct(
        string $id,
        ?string $parentId,
        string $treeId,
        string $path,
        int $depth,
        string $slug,
        string $slugPath,
        string $locale,
    ) {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->treeId = $treeId;
        $this->path = $path;
        $this->depth = $depth;
        $this->slug = $slug;
        $this->slugPath = $slugPath;
        $this->locale = $locale;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }

    public function treeId(): string
    {
        return $this->treeId;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function slugPath(): string
    {
        return $this->slugPath;
    }

    public function locale(): string
    {
        return $this->locale;
    }
}
