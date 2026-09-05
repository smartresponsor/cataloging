<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Listener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Applies catalog security headers to HTTP responses.
 */
final class SecurityHeaderListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; style-src 'self' 'unsafe-inline'"
        );
    }
}
