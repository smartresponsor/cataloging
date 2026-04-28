<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a durable membership record for a virtual category.
 */
#[ORM\Entity]
#[ORM\Table(name: 'virtual_category_member')]
#[ORM\Index(name: 'idx_virtual_category_member_record', columns: ['record_id'])]
class CatalogVirtualCategoryMemberEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private null $id = null;

    #[ORM\Column(type: 'string', length: 26, name: 'virtual_category_id')]
    private string $virtualCategoryId;

    #[ORM\Column(type: 'string', length: 64, name: 'record_id')]
    private string $recordId;

    public function __construct(string $virtualCategoryId, string $recordId)
    {
        $this->virtualCategoryId = $virtualCategoryId;
        $this->recordId = $recordId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVirtualCategoryId(): string
    {
        return $this->virtualCategoryId;
    }

    public function getRecordId(): string
    {
        return $this->recordId;
    }
}
