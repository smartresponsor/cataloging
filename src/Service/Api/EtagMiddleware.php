<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Api;

/**
 * Thin API wrapper kept for backward compatibility.
 * Canonical implementation lives in App\Service\EtagMiddleware.
 */
final class EtagMiddleware
{
    private \App\Service\EtagMiddleware $inner;

    public function __construct(?\App\Service\EtagMiddleware $inner = null)
    {
        $this->inner = $inner ?? new \App\Service\EtagMiddleware();
    }

    public function handle(array $request, array $response, callable $next): array
    {
        return $this->inner->handle($request, $response, $next);
    }
}
