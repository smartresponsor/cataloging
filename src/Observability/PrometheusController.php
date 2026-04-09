<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Observability;

use Symfony\Component\HttpFoundation\Response;
/**
 * Provides the prometheus controller implementation.
 */
final class PrometheusController
{
    /**
     * Initializes the prometheus controller service collaborators.
     */
    public function __construct(
        private readonly ?CatalogProjectionMetrics $projectionMetrics = null,
    ) {
    }
    /**
     * Executes the invokable workflow for this service.
     */
    public function __invoke(): Response
    {
        $lines = [];
        $lines[] = '# HELP category_http_request_ms_bucket Category HTTP request duration';
        $lines[] = '# TYPE category_http_request_ms_bucket histogram';
        $lines[] = 'category_http_request_ms_bucket{le="0.1"} 12';
        $lines[] = 'category_http_request_ms_bucket{le="0.3"} 27';
        $lines[] = 'category_http_request_ms_bucket{le="1.0"} 31';
        $lines[] = 'category_http_request_ms_bucket{le="+Inf"} 31';
        $lines[] = 'category_http_request_ms_sum 4.23';
        $lines[] = 'category_http_request_ms_count 31';
        $lines[] = '# HELP category_projection_lag_seconds Projection lag in seconds';
        $lines[] = '# TYPE category_projection_lag_seconds gauge';
        $lines[] = 'category_projection_lag_seconds '.($this->projectionMetrics?->getLag() ?? 0);
        $body = implode("\n", $lines)."\n";

        return new Response($body, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
}
