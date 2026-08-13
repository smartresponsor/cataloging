<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use App\Objecting\EntityInterface\ObjectEntityInterface;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectIdentityEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectStateEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectTitleEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the durable category write-model entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'category')]
#[ORM\UniqueConstraint(name: 'uniq_category_catalog_parent_slug', columns: ['catalog_id', 'parent_id', 'slug'])]
#[ORM\Index(name: 'idx_category_path', columns: ['path'])]
#[ORM\Index(name: 'idx_category_catalog_path', columns: ['catalog_id', 'path'])]
#[ORM\Index(name: 'idx_category_tenant_workflow', columns: ['tenant', 'workflow_state'])]
/** @noinspection PhpPropertyNamingConventionInspection */
class CatalogCategoryEntity implements ObjectEntityInterface
{
    use ObjectIdentityEmbeddableTrait;
    use ObjectTitleEmbeddableTrait;
    use ObjectAuditEmbeddableTrait;
    use ObjectStateEmbeddableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 160)]
    private string $nameEntity;

    #[ORM\Column(type: 'string', length: 36)]
    private string $slug;

    #[ORM\ManyToOne(targetEntity: CatalogCatalogEntity::class)]
    #[ORM\JoinColumn(name: 'catalog_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private CatalogCatalogEntity $catalog;

    #[ORM\Column(type: 'integer', nullable: true, name: 'parent_id')]
    private ?int $parentId = null;

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
        CatalogCatalogEntity $catalog,
        string $nameEntity,
        string $slug,
        string $path,
        int $depth,
        int|string|null $parentId = null,
        ?string $locale = null,
        string $tenant = 'default',
    ) {
        $normalizedTenant = '' === trim($tenant) ? 'default' : trim($tenant);
        $this->catalog = $catalog;
        $this->nameEntity = $nameEntity;
        $this->slug = $slug;
        $this->path = $path;
        $this->depth = $depth;
        $this->parentId = self::normalizeNullableIntegerId($parentId, 'parent_id');
        $this->locale = $locale;
        $this->tenant = $normalizedTenant;
        $this->initializeObjectIdentity(objectSlug: $normalizedTenant.':'.$catalog->getCode().':'.$path);
        $this->initializeObjectTitle($nameEntity);
        $this->initializeObjectAudit();
        $this->initializeObjectState(objectStatus: $this->workflowState);
    }

    public function getCatalog(): CatalogCatalogEntity
    {
        return $this->catalog;
    }

    /**
     * Returns the id value.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the nameEntity value.
     */
    public function getName(): string
    {
        return $this->nameEntity;
    }

    /**
     * Updates the nameEntity value.
     */
    public function setName(string $nameEntity): void
    {
        $this->nameEntity = $nameEntity;
        $this->setFirstTitle($nameEntity);
        $this->touchModified();
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
        return null === $this->parentId ? null : (string) $this->parentId;
    }

    /**
     * Updates the parent id value.
     */
    public function setParentId(?string $parentId): void
    {
        $this->parentId = self::normalizeNullableIntegerId($parentId, 'parent_id');
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
        $this->synchronizeObjectSlug();
        $this->touchModified();
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
        $this->tenant = '' === trim($tenant) ? 'default' : trim($tenant);
        $this->synchronizeObjectSlug();
        $this->touchModified();
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
        $this->setObjectStatus($workflowState);
        $this->touchModified();
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
        if ($this->catalog->getCode() !== $parent->catalog->getCode()) {
            return false;
        }

        if ($this->depth !== $parent->depth + 1) {
            return false;
        }

        return $this->getParentPath() === $parent->path;
    }

    private static function normalizeNullableIntegerId(int|string|null $value, string $field): ?int
    {
        if (null === $value) {
            return null;
        }

        $normalized = is_int($value) ? (string) $value : trim($value);
        if ('' === $normalized) {
            return null;
        }

        if (!ctype_digit($normalized) || '0' === $normalized) {
            throw new \InvalidArgumentException($field.' must be a positive integer id.');
        }

        return (int) $normalized;
    }

    private function synchronizeObjectSlug(): void
    {
        $this->setObjectSlug($this->tenant.':'.$this->catalog->getCode().':'.$this->path);
    }
}
