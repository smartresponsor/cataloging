<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Observability;

use App\Util\RotatingFileWriter;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

final class MetricsSubscriber implements EventSubscriberInterface
{
    private float $start = 0.0;
    private LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequest',
            TerminateEvent::class => 'onTerminate',
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $this->start = microtime(true);
    }

    public function onTerminate(TerminateEvent $event): void
    {
        $elapsed = (microtime(true) - $this->start) * 1000.0;
        $status = $event->getResponse()->getStatusCode();
        $record = [
            'ts' => (new \DateTimeImmutable())->format(DATE_ATOM),
            'path' => $event->getRequest()->getPathInfo(),
            'ms' => round($elapsed, 2),
            'status' => $status,
        ];

        try {
            $writer = new RotatingFileWriter(sys_get_temp_dir().'/sr_metrics/category_http.jsonl');
            $writer->write((string) json_encode($record, JSON_THROW_ON_ERROR)."\n");
        } catch (\Throwable $throwable) {
            $this->logger->error('Category HTTP metrics could not be persisted.', [
                'record' => $record,
                'exception' => $throwable,
            ]);
        }
    }
}
