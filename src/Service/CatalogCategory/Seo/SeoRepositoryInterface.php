<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\CatalogCategory\Seo;

interface SeoRepositoryInterface
{
    /**
     * @param array{categoryId: string, locale: string, canonicalUrl?: string|null, noindex?: bool, nofollow?: bool} $input
     */
    public function save(array $input): void;

    /**
     * @return array{categoryId: string, locale: string, canonicalUrl?: string|null, noindex: bool, nofollow: bool}|null
     */
    public function find(string $categoryId, string $locale): ?array;
}
