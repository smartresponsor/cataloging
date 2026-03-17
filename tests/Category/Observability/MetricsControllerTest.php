<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Observability;

use App\Observability\MetricsController;
use PHPUnit\Framework\TestCase;

final class MetricsControllerTest extends TestCase
{
    public function testMetricsReturnsPrometheusTextWithProjectionLagGauge(): void
    {
        $metricsDir = sys_get_temp_dir().'/sr_metrics';
        @mkdir($metricsDir, 0o755, true);
        file_put_contents($metricsDir.'/category_http.jsonl', "{}\n{}\n");
        @mkdir('report', 0o755, true);
        file_put_contents('report/category-projection-lag.json', json_encode(['lag' => 7], JSON_THROW_ON_ERROR));

        $response = (new MetricsController())->metrics();
        $body = $response->getContent();

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('category_http_requests_total 2', (string) $body);
        self::assertStringContainsString('category_projection_lag_seconds 7', (string) $body);
        self::assertStringContainsString('text/plain; version=0.0.4', (string) $response->headers->get('Content-Type'));
    }
}
