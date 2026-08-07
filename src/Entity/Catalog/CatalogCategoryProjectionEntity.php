<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the durable category projection read-model entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'category_projection')]
#[ORM\Index(name: 'idx_category_projection_path', columns: ['path'])]
#[ORM\Index(name: 'idx_category_projection_tenant_locale', columns: ['tenant', 'locale'])]
#[ORM\Index(name: 'idx_category_projection_workflow_state', columns: ['workflow_state'])]
#[ORM\Index(name: 'idx_category_projection_updated_at', columns: ['updated_at'])]
class CatalogCategoryProjectionEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 26, options: ['fixed' => true])]
    private string $id;

    #[ORM\Column(type: 'string', length: 180)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 160)]
    private string $nameEntity;

    #[ORM\Column(type: 'string', length: 26, nullable: true, options: ['fixed' => true])]
    private ?string $parentId = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $path;

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

    #[ORM\Column(type: 'datetime_immutable', name: 'updated_at')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getName(): string
    {
        return $this->nameEntity;
    }

    public function setName(string $nameEntity): void
    {
        $this->nameEntity = $nameEntity;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getTenant(): string
    {
        return $this->tenant;
    }

    public function setTenant(string $tenant): void
    {
        $normalized = trim($tenant);
        $this->tenant = '' === $normalized ? 'default' : $normalized;
    }

    public function getWorkflowState(): string
    {
        return $this->workflowState;
    }

    public function setWorkflowState(string $workflowState): void
    {
        $this->workflowState = $workflowState;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function setIconUrl(?string $iconUrl): void
    {
        $this->iconUrl = $iconUrl;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
