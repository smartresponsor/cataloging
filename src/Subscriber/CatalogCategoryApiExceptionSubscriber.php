<?php

declare(strict_types=1);

namespace App\Subscriber;

use App\Exception\CatalogCategoryNotFoundException;
use Doctrine\DBAL\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Converts catalog category API exceptions to normalized JSON responses.
 */
final class CatalogCategoryApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $routeName = (string) $request->attributes->get('_route', '');
        if (!str_starts_with($routeName, 'api_catalog_category_')) {
            return;
        }

        $exception = $event->getThrowable();
        if ($exception instanceof AccessDeniedHttpException) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], 403));

            return;
        }

        if ($exception instanceof \InvalidArgumentException) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], 400));

            return;
        }

        if ($exception instanceof \DomainException) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], 409));

            return;
        }

        if ($exception instanceof CatalogCategoryNotFoundException) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], 404));

            return;
        }

        if ($exception instanceof \RuntimeException) {
            $event->setResponse(new JsonResponse(['error' => $exception->getMessage()], 409));

            return;
        }

        if ($exception instanceof Exception) {
            $event->setResponse(new JsonResponse(['error' => 'Catalog category API runtime failure.'], 500));

            return;
        }

        $event->setResponse(new JsonResponse(['error' => 'Catalog category API unexpected failure.'], 500));
    }
}
