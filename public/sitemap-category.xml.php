<?php
declare(strict_types=1);
use App\Service\Seo\Category\SitemapGenerator;
use App\Service\Query\Category\CanonicalResolver;
$resolver = new CanonicalResolver();
$generator = new SitemapGenerator($resolver);
$categories = [ ['slug' => 'root'], ['slug' => 'electronics'], ['slug' => 'draft', 'noindex' => true] ];
header('Content-Type: application/xml');
echo $generator->generate($categories, 'en');
