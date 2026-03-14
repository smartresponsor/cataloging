<?php

declare(strict_types=1);

namespace App\Observability;

use Symfony\Component\HttpFoundation\Response;

final class PrometheusController
{
    public function __invoke(): Response
    {
        // dummy metrics; real values must be wired to storage
        $lines = [];
        $lines[] = '# HELP category_http_request_ms_bucket Category HTTP request duration';
        $lines[] = '# TYPE category_http_request_ms_bucket histogram';
        $lines[] = 'category_http_request_ms_bucket{le="0.1"} 12';
        $lines[] = 'category_http_request_ms_bucket{le="0.3"} 27';
        $lines[] = 'category_http_request_ms_bucket{le="1.0"} 31';
        $lines[] = 'category_http_request_ms_bucket{le="+Inf"} 31';
        $lines[] = 'category_http_request_ms_sum 4.23';
        $lines[] = 'category_http_request_ms_count 31';
        $body = implode("\n", $lines)."\n";

        return new Response($body, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
}
