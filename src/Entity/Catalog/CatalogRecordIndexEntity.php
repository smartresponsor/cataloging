<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the durable record index read-model entity.
 */
#[ORM\Entity]
#[ORM\Table(name: 'record_index')]
#[ORM\Index(name: 'idx_record_index_brand', columns: ['brand'])]
#[ORM\Index(name: 'idx_record_index_price', columns: ['price'])]
#[ORM\Index(name: 'idx_record_index_stock', columns: ['stock'])]
class CatalogRecordIndexEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $id;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stock = null;

    /** @var list<bool|float|int|string>|null */
    #[ORM\Column(type: 'json', nullable: true, name: 'tag_set')]
    private ?array $tagSet = null;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = null === $brand ? null : trim($brand);
    }

    public function getPrice(): ?float
    {
        return null === $this->price ? null : (float) $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = null === $price ? null : number_format($price, 2, '.', '');
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }

    /** @return list<bool|float|int|string>|null */
    public function getTagSet(): ?array
    {
        return $this->tagSet;
    }

    /** @param list<bool|float|int|string>|null $tagSet */
    public function setTagSet(?array $tagSet): void
    {
        $this->tagSet = $tagSet;
    }
}
