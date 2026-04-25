<?php

declare(strict_types=1);

namespace App\Cataloging\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a durable SEO redirect rule keyed by source path.
 */
#[ORM\Entity]
#[ORM\Table(name: 'seo_redirect')]
class CatalogSeoRedirectEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255, name: 'from_path')]
    private string $fromPath;

    #[ORM\Column(type: 'string', length: 255, name: 'to_path')]
    private string $toPath;

    #[ORM\Column(type: 'integer')]
    private int $status;

    public function __construct(string $fromPath, string $toPath, int $status)
    {
        $this->fromPath = trim($fromPath);
        $this->toPath = trim($toPath);
        $this->status = $status;
    }

    public function fromPath(): string
    {
        return $this->fromPath;
    }

    public function toPath(): string
    {
        return $this->toPath;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function changeToPath(string $toPath): void
    {
        $this->toPath = trim($toPath);
    }

    public function changeStatus(int $status): void
    {
        $this->status = $status;
    }
}
