<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * Represents the durable category write-model entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[ORM\Index(name: 'idx_category_path', columns: ['path'])]
#[ORM\Index(name: 'idx_category_tenant_workflow', columns: ['tenant', 'workflow_state'])]
/** @noinspection PhpPropertyNamingConventionInspection */
class CatalogCategoryEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(type: 'string', length: 160)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 26, nullable: true, options: ['fixed' => true], name: 'parent_id')]
    private ?string $parentId = null;

    // Stored in Postgres as ltree via a custom Doctrine type.
    #[ORM\Column(type: 'ltree')]
    private string $path;

    #[ORM\Column(type: 'integer')]
    private int $depth;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private ?string $locale = null;

    #[ORM\Column(type: 'string', length: 64, options: ['default' => 'default'])]
    private string $tenant = 'default';

    #[ORM\Column(type: 'string', length: 32, options: ['default' => 'draft'], name: 'workflow_state')]
    private string $workflowState = 'draft';

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $published = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true, name: 'published_at')]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true, name: 'icon_url')]
    private ?string $iconUrl = null;

    /**
     * Initializes the durable category write-model.
     */
    public function __construct(
        string $name,
        string $slug,
        string $path,
        int $depth,
        ?string $parentId = null,
        ?string $locale = null,
        string $tenant = 'default',
    ) {
        $this->id = (string) new Ulid();
        $this->name = $name;
        $this->slug = $slug;
        $this->path = $path;
        $this->depth = $depth;
        $this->parentId = $parentId;
        $this->locale = $locale;
        $this->tenant = '' === trim($tenant) ? 'default' : $tenant;
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
     * Returns the parent id value.
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * Updates the parent id value.
     */
    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
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
     * Returns the locale value.
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Updates the locale value.
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * Returns the tenant value.
     */
    public function getTenant(): string
    {
        return $this->tenant;
    }

    /**
     * Updates the tenant value.
     */
    public function setTenant(string $tenant): void
    {
        $this->tenant = '' === trim($tenant) ? 'default' : $tenant;
    }

    /**
     * Returns the workflow state value.
     */
    public function getWorkflowState(): string
    {
        return $this->workflowState;
    }

    /**
     * Updates the workflow state value.
     */
    public function setWorkflowState(string $workflowState): void
    {
        $this->workflowState = $workflowState;
    }

    /**
     * Returns the published flag.
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * Updates the published flag.
     */
    public function setPublished(bool $published): void
    {
        $this->published = $published;
        if (!$published) {
            $this->publishedAt = null;
        }
    }

    /**
     * Returns the published at value.
     */
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * Updates the published at value.
     */
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    /**
     * Returns the icon url value.
     */
    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    /**
     * Updates the icon url value.
     */
    public function setIconUrl(?string $iconUrl): void
    {
        $this->iconUrl = $iconUrl;
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
