<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Subscriber;

use App\Cataloging\Service\CatalogCacheInvalidationRecorderService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribes to category changes and invalidates catalog cache entries.
 */
final readonly class CategoryChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(private CatalogCacheInvalidationRecorderService $invalidator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'category.created' => 'onChanged',
            'category.moved' => 'onChanged',
            'category.published' => 'onChanged',
        ];
    }

    public function onChanged(object $event): void
    {
        $id = $event->id ?? null;
        if (is_int($id) || is_string($id)) {
            $this->invalidator->invalidate($id);
        }
    }
}
