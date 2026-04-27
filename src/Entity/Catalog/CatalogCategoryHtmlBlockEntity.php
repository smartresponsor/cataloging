<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the category html block domain record.
 */
#[ORM\Entity]
#[ORM\Table(name: 'catalog_category_html_block')]
class CatalogCategoryHtmlBlockEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(type: 'text')]
    private string $html;

    #[ORM\Column(type: 'boolean')]
    private bool $isDraft = true;

    /**
     * Initializes the category html block service collaborators.
     */
    public function __construct(string $categoryId, string $html)
    {
        $this->categoryId = $categoryId;
        $this->html = $html;
    }

    /**
     * Handles the id workflow.
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the html workflow.
     */
    public function html(): string
    {
        return $this->html;
    }

    /**
     * Determines whether the draft condition is satisfied.
     */
    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    /**
     * Handles the publish workflow.
     */
    public function publish(): void
    {
        $this->isDraft = false;
    }
}
