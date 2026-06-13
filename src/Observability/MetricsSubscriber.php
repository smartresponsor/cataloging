<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Observability;

use App\Cataloging\Util\RotatingFileWriter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

/**
 * Provides the metrics subscriber implementation.
 */
final class MetricsSubscriber implements EventSubscriberInterface
{
    private float $start = 0.0;

    /**
     * Initializes the metrics subscriber service collaborators.
     */
    public function __construct(private readonly RequestCorrelationIdProvider $requestCorrelationIdProvider)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequest',
            TerminateEvent::class => 'onTerminate',
        ];
    }

    /**
     * Handles the on request workflow.
     */
    public function onRequest(RequestEvent $requestEvent): void
    {
        if (!$requestEvent->isMainRequest()) {
            return;
        }

        $request = $requestEvent->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api/catalog/category/')) {
            return;
        }

        $this->start = microtime(true);
    }

    /**
     * Handles the on terminate workflow.
     */
    public function onTerminate(TerminateEvent $e): void
    {
        $request = $e->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api/catalog/category/')) {
            return;
        }

        $elapsed = (microtime(true) - $this->start) * 1000.0;
        $status = $e->getResponse()->getStatusCode();
        $timestamp = new \DateTimeImmutable();
        $rec = [
            'ts' => $timestamp->format(DATE_ATOM),
            'path' => $request->getPathInfo(),
            'ms' => round($elapsed, 2),
            'status' => $status,
            'correlation_id' => $this->requestCorrelationIdProvider->current(),
        ];
        $metricsDir = sys_get_temp_dir().'/sr_metrics';
        $this->ensureDirectory($metricsDir);
        $writer = new RotatingFileWriter($metricsDir.'/category_http.jsonl');
        $writer->write(json_encode($rec)."\n");
    }

    private function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0o755, true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Unable to create directory: %s', $path));
        }
    }
}
