<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use App\Cataloging\Repository\Catalog\CatalogCategoryProductBindingRepository;
use App\Objecting\EntityInterface\ObjectRelationEntityInterface;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectIdentityEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectStateEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogCategoryProductBindingRepository::class)]
#[ORM\Table(name: 'category_product_binding')]
#[ORM\UniqueConstraint(name: 'uniq_category_product_binding', columns: ['category_id', 'product_id'])]
final class CatalogCategoryProductBindingEntity implements ObjectRelationEntityInterface
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
        #[ORM\Column(name: 'product_id', length: 64)]
        private string $productId,
        #[ORM\Column(name: 'sort_order', type: 'integer')]
        private int $sortOrder = 0,
        #[ORM\Column(type: 'boolean')]
        private bool $primaryCategory = false,
    ) {
        $this->initializeObjectIdentity(objectSlug: $categoryId.'-'.$productId);
        $this->initializeObjectAudit();
        $this->initializeObjectState(objectStatus: 'active');
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    public function primaryCategory(): bool
    {
        return $this->primaryCategory;
    }
}
