<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Category;

use OpenTelemetry\API\Trace\TracerProviderInterface;

final class TracingDecorator
{
    public function __construct(private readonly TracerProviderInterface $tracerProvider)
    {
    }

    public function trace(string $name, callable $fn): mixed
    {
        $tracer = $this->tracerProvider->getTracer('category');
        $span = $tracer->spanBuilder($name)->startSpan();
        try {
            return $fn();
        } finally {
            $span->end();
        }
    }
}
