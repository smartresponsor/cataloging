<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class CatalogCategoryCacheHeader
{
    public function make(string $etag, ?\DateTimeImmutable $lastModified = null): array
    {
        $headers = ['ETag' => $etag];
        if ($lastModified) {
            $headers['Last-Modified'] = $lastModified->format('D, d M Y H:i:s').' GMT';
        }
        $headers['Cache-Control'] = 'public, max-age=60';

        return $headers;
    }
}
