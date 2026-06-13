<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use App\Cataloging\Repository\Catalog\CatalogCategoryFeaturedRepository;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectIdentityEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectStateEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogCategoryFeaturedRepository::class)]
#[ORM\Table(name: 'category_featured')]
#[ORM\Index(name: 'idx_category_featured_category', columns: ['category_id'])]
#[ORM\Index(name: 'idx_category_featured_surface', columns: ['surface', 'active'])]
final class CatalogCategoryFeaturedEntity
{
    use ObjectIdentityEmbeddableTrait;
    use ObjectAuditEmbeddableTrait;
    use ObjectStateEmbeddableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(name: 'category_id', length: 26)]
        private string $categoryId,
        #[ORM\Column(length: 64)]
        private string $surface,
        #[ORM\Column(name: 'sort_order', type: 'integer')]
        private int $sortOrder = 0,
        #[ORM\Column(type: 'boolean')]
        private bool $active = true,
        #[ORM\Column(name: 'starts_at', type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $startsAt = null,
        #[ORM\Column(name: 'ends_at', type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $endsAt = null,
    ) {
        $this->initializeObjectIdentity(objectSlug: $categoryId.'-'.$surface);
        $this->initializeObjectAudit();
        $this->initializeObjectState(objectStatus: $active ? 'active' : 'inactive');
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function surface(): string
    {
        return $this->surface;
    }

    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function startsAt(): ?\DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function endsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }
}
