<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use App\Cataloging\Repository\Catalog\CatalogCategoryAttachmentTranslationRepository;
use App\Objecting\EntityTrait\Embeddable\ObjectAuditEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectIdentityEmbeddableTrait;
use App\Objecting\EntityTrait\Embeddable\ObjectLocaleEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogCategoryAttachmentTranslationRepository::class)]
#[ORM\Table(name: 'category_attachment_translation')]
#[ORM\UniqueConstraint(name: 'uniq_category_attachment_translation_locale', columns: ['attachment_id', 'locale'])]
final class CatalogCategoryAttachmentTranslationEntity
{
    use ObjectIdentityEmbeddableTrait;
    use ObjectAuditEmbeddableTrait;
    use ObjectLocaleEmbeddableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(name: 'attachment_id', length: 26)]
        private string $attachmentId,
        #[ORM\Column(length: 12)]
        private string $locale,
        #[ORM\Column(length: 180, nullable: true)]
        private ?string $title = null,
        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $alt = null,
    ) {
        $this->initializeObjectIdentity(objectSlug: $attachmentId.'-'.$locale);
        $this->initializeObjectAudit();
        $this->initializeObjectLocale(objectLocale: $locale);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function attachmentId(): string
    {
        return $this->attachmentId;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function alt(): ?string
    {
        return $this->alt;
    }
}
