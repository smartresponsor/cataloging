<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use App\Cataloging\Repository\Catalog\CatalogCategoryTranslationRepository;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectIdentityEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectLocaleEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogCategoryTranslationRepository::class)]
#[ORM\Table(name: 'category_translation')]
#[ORM\UniqueConstraint(name: 'uniq_category_translation_locale', columns: ['category_id', 'locale'])]
final class CatalogCategoryTranslationEntity
{
    use ObjectIdentityEmbeddableTrait;
    use ObjectAuditEmbeddableTrait;
    use ObjectLocaleEmbeddableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(name: 'category_id', length: 26)]
        private string $categoryId,
        #[ORM\Column(length: 12)]
        private string $locale,
        #[ORM\Column(length: 180)]
        private string $nameEntity,
        #[ORM\Column(length: 180)]
        private string $slug,
        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $description = null,
        #[ORM\Column(name: 'meta_title', length: 180, nullable: true)]
        private ?string $metaTitle = null,
        #[ORM\Column(name: 'meta_description', type: 'text', nullable: true)]
        private ?string $metaDescription = null,
    ) {
        $this->initializeObjectIdentity(objectSlug: $categoryId.'-'.$locale);
        $this->initializeObjectAudit();
        $this->initializeObjectLocale(objectLocale: $locale);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function nameEntity(): string
    {
        return $this->nameEntity;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function metaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function metaDescription(): ?string
    {
        return $this->metaDescription;
    }
}
