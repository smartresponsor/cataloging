<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\CacheInvalidationRecorder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CategoryChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CacheInvalidationRecorder $invalidator)
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
        if (null !== $id) {
            $this->invalidator->invalidate($id);
        }
    }
}
