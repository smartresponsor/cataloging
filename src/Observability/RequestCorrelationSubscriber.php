<?php

declare(strict_types=1);

namespace App\Observability;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
/**
 * Provides the request correlation subscriber implementation.
 */
final class RequestCorrelationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onRequest', 256],
            ResponseEvent::class => ['onResponse', -256],
        ];
    }
    /**
     * Handles the on request workflow.
     */
    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $request->attributes->set(
            RequestCorrelationIdProvider::ATTRIBUTE,
            $this->resolveCorrelationId($request),
        );
    }
    /**
     * Handles the on response workflow.
     */
    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $correlationId = $event->getRequest()->attributes->get(RequestCorrelationIdProvider::ATTRIBUTE);
        if (!is_string($correlationId) || '' === $correlationId) {
            return;
        }

        $event->getResponse()->headers->set(RequestCorrelationIdProvider::HEADER, $correlationId);
    }

    private function resolveCorrelationId(Request $request): string
    {
        $headerValue = $request->headers->get(RequestCorrelationIdProvider::HEADER);
        if (is_string($headerValue)) {
            $headerValue = trim($headerValue);

            if ('' !== $headerValue) {
                return $headerValue;
            }
        }

        return $this->generateCorrelationId();
    }

    private function generateCorrelationId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
