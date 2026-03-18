<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Observability;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

final class MetricsSubscriber implements EventSubscriberInterface
{
    private float $start = 0.0;

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequest',
            TerminateEvent::class => 'onTerminate',
        ];
    }

    public function onRequest(RequestEvent $e): void
    {
        $this->start = microtime(true);
    }

    public function onTerminate(TerminateEvent $e): void
    {
        $elapsed = (microtime(true) - $this->start) * 1000.0;
        $status = $e->getResponse()->getStatusCode();
        $rec = [
            'ts' => (new \DateTimeImmutable())->format(DATE_ATOM),
            'path' => $e->getRequest()->getPathInfo(),
            'ms' => round($elapsed, 2),
            'status' => $status,
        ];
        $metricsDir = sys_get_temp_dir().'/sr_metrics';
        $this->ensureDirectory($metricsDir);
        $writer = new \App\Util\RotatingFileWriter($metricsDir.'/category_http.jsonl');
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
