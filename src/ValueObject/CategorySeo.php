<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/** SEO value object: localized full slug and breadcrumb title. */
final class CategorySeo
{
    private string $fullSlug;
    private string $title;

    public function __construct(string $fullSlug, string $title)
    {
        $this->fullSlug = $fullSlug;
        $this->title = $title;
    }

    public function fullSlug(): string
    {
        return $this->fullSlug;
    }

    public function title(): string
    {
        return $this->title;
    }
}
