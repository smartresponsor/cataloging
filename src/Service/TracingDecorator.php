<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the tracing decorator application service.
 */
final class TracingDecorator
{
    /**
     * Initializes the tracing decorator service collaborators.
     */
    public function __construct(private readonly ?object $tracerProvider = null)
    {
    }
    /**
     * Handles the trace workflow.
     */
    public function trace(string $name, callable $fn): mixed
    {
        $span = null;
        $provider = $this->tracerProvider;
        if (is_object($provider) && method_exists($provider, 'getTracer')) {
            $tracer = $provider->getTracer('category');
            if (is_object($tracer) && method_exists($tracer, 'spanBuilder')) {
                $builder = $tracer->spanBuilder($name);
                if (is_object($builder) && method_exists($builder, 'startSpan')) {
                    $span = $builder->startSpan();
                }
            }
        }

        try {
            return $fn();
        } finally {
            if (is_object($span) && method_exists($span, 'end')) {
                $span->end();
            }
        }
    }
}
