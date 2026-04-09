<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the category cache header application service.
 */
final class CategoryCacheHeader
{
    /** @return array{ETag:string,Cache-Control:string,Last-Modified?:string} */
    public function make(string $etag, ?\DateTimeImmutable $lastModified = null): array
    {
        $headers = ['ETag' => $etag, 'Cache-Control' => 'public, max-age=60'];
        if (null !== $lastModified) {
            $headers['Last-Modified'] = $lastModified->format('D, d M Y H:i:s').' GMT';
        }

        return $headers;
    }
}
