<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use App\Objecting\EntityInterface\ObjectCodedInterface;
use App\Objecting\EntityInterface\ObjectEntityInterface;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectCodeEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectIdentityEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectStateEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectTitleEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents one durable catalog namespace and its business purpose.
 */
#[ORM\Entity]
#[ORM\Table(name: 'catalog')]
#[ORM\UniqueConstraint(name: 'uniq_catalog_tenant_code', columns: ['tenant', 'object_code'])]
final class CatalogCatalogEntity implements ObjectEntityInterface, ObjectCodedInterface
{
    use ObjectIdentityEmbeddableTrait;
    use ObjectTitleEmbeddableTrait;
    use ObjectAuditEmbeddableTrait;
    use ObjectCodeEmbeddableTrait;
    use ObjectStateEmbeddableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 160)]
    private string $name;

    #[ORM\Column(type: 'string', length: 48)]
    private string $purpose;

    #[ORM\Column(type: 'string', length: 64, options: ['default' => 'default'])]
    private string $tenant = 'default';

    public function __construct(string $code, string $name, string $purpose, string $tenant = 'default')
    {
        $normalizedCode = trim($code);
        $normalizedName = trim($name);
        $normalizedPurpose = trim($purpose);
        if ('' === $normalizedCode || '' === $normalizedName || '' === $normalizedPurpose) {
            throw new \InvalidArgumentException('Catalog code, name, and purpose are required.');
        }

        $normalizedTenant = '' === trim($tenant) ? 'default' : trim($tenant);
        $this->initializeObjectIdentity(objectSlug: $normalizedTenant.':'.$normalizedCode);
        $this->initializeObjectTitle($normalizedName);
        $this->initializeObjectCode($normalizedCode);
        $this->initializeObjectState(true, true, 'active');
        $this->initializeObjectAudit();
        $this->name = $normalizedName;
        $this->purpose = $normalizedPurpose;
        $this->tenant = $normalizedTenant;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->getObjectCode() ?? '';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $normalized = trim($name);
        if ('' === $normalized) {
            throw new \InvalidArgumentException('Catalog name is required.');
        }
        $this->name = $normalized;
        $this->setFirstTitle($normalized);
        $this->touchModified();
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function setPurpose(string $purpose): void
    {
        $normalized = trim($purpose);
        if ('' === $normalized) {
            throw new \InvalidArgumentException('Catalog purpose is required.');
        }
        $this->purpose = $normalized;
        $this->touchModified();
    }

    public function getTenant(): string
    {
        return $this->tenant;
    }
}
