<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class EtagMiddleware
{
    public function handle(array $request, array $response, callable $next): array
    {
        $etag = $response['headers']['ETag'] ?? null;
        $ifNone = $request['headers']['If-None-Match'] ?? null;
        if ($etag && $ifNone && $etag === $ifNone) {
            return ['status' => 304, 'headers' => ['ETag' => $etag], 'body' => ''];
        }

        return $next($request, $response);
    }
}
