<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventSubscriber;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Provides the security header listener implementation.
 */
final class SecurityHeaderListener
{
    /**
     * Handles the on kernel response workflow.
     */
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
