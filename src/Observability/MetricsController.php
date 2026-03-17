<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Observability;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MetricsController extends AbstractController
{
    #[Route('/metrics', name: 'metrics', methods: ['GET'])]
    public function metrics(): Response
    {
        $path = sys_get_temp_dir().'/sr_metrics/category_http.jsonl';
        $requestCount = 0;

        if (file_exists($path)) {
            $contents = file_get_contents($path);
            if (false !== $contents && '' !== trim($contents)) {
                $requestCount = count(array_filter(explode("\n", trim($contents))));
            }
        }

        $projectionLag = $this->readProjectionLag();

        $lines = [];
        $lines[] = '# HELP category_http_requests_total Count of observed category HTTP requests';
        $lines[] = '# TYPE category_http_requests_total counter';
        $lines[] = sprintf('category_http_requests_total %d', $requestCount);
        $lines[] = '# HELP category_projection_lag_seconds Current projection lag';
        $lines[] = '# TYPE category_projection_lag_seconds gauge';
        $lines[] = sprintf('category_projection_lag_seconds %d', $projectionLag);
        $body = implode("\n", $lines)."\n";

        return new Response($body, 200, ['Content-Type' => 'text/plain; version=0.0.4; charset=utf-8']);
    }

    private function readProjectionLag(): int
    {
        $reportPath = 'report/category-projection-lag.json';
        if (!file_exists($reportPath)) {
            return 0;
        }

        $decoded = json_decode((string) file_get_contents($reportPath), true);
        if (!is_array($decoded)) {
            return 0;
        }

        return (int) ($decoded['lag'] ?? $decoded['seconds'] ?? 0);
    }
}
