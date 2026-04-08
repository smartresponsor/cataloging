<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/** SEO value object: localized full slug and breadcrumb title. */
final class CategorySeo
{
    private string $fullSlug;
    private string $title;
    /**
     * Initializes the category seo service collaborators.
     */
    public function __construct(string $fullSlug, string $title)
    {
        $this->fullSlug = $fullSlug;
        $this->title = $title;
    }
    /**
     * Handles the full slug workflow.
     */
    public function fullSlug(): string
    {
        return $this->fullSlug;
    }
    /**
     * Handles the title workflow.
     */
    public function title(): string
    {
        return $this->title;
    }
}
