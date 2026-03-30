<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class EtagMiddleware
{
    /**
     * @param array{headers?:array<string,string>}                                                                                               $request
     * @param array{headers?:array<string,string>,status?:int,body?:string}                                                                      $response
     * @param callable(array{headers?:array<string,string>}, array{headers?:array<string,string>,status?:int,body?:string}): array<string,mixed> $next
     *
     * @return array<string,mixed>
     */
    public function handle(array $request, array $response, callable $next): array
    {
        $etag = $response['headers']['ETag'] ?? null;
        $ifNone = $request['headers']['If-None-Match'] ?? null;
        if (is_string($etag) && '' !== $etag && is_string($ifNone) && $etag === $ifNone) {
            return ['status' => 304, 'headers' => ['ETag' => $etag], 'body' => ''];
        }

        return $next($request, $response);
    }
}
