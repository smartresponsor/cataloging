<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'redirect_rule')]
#[ORM\UniqueConstraint(name: 'uniq_redirect_rule_locale_from', columns: ['locale', 'from_path'])]
class CatalogRedirectRuleEntity
{
    #[ORM\Id]
    #[ORM\Column(name: 'from_path', type: 'string', length: 2048)]
    private string $fromPath;

    #[ORM\Column(name: 'to_path', type: 'string', length: 2048)]
    private string $toPath;

    #[ORM\Column(type: 'string', length: 16, nullable: true)]
    private ?string $locale;

    #[ORM\Column(type: 'string', length: 64)]
    private string $source;

    public function __construct(string $fromPath, string $toPath, ?string $locale, string $source)
    {
        $this->fromPath = $fromPath;
        $this->toPath = $toPath;
        $this->locale = $locale;
        $this->source = $source;
    }

    public function fromPath(): string
    {
        return $this->fromPath;
    }

    public function toPath(): string
    {
        return $this->toPath;
    }

    public function locale(): ?string
    {
        return $this->locale;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function changeToPath(string $toPath): void
    {
        $this->toPath = $toPath;
    }
}
