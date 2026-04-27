<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */
use App\Service\CatalogCanonicalResolverService;
use App\Service\CatalogSitemapGeneratorService;

$resolver = new CatalogCanonicalResolverService();
$generator = new CatalogSitemapGeneratorService($resolver);
$categories = [['slug' => 'root'], ['slug' => 'electronics'], ['slug' => 'draft', 'noindex' => true]];
header('Content-Type: application/xml');
echo $generator->generate($categories, 'en');
