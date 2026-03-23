<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class CategoryCacheHeader
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
