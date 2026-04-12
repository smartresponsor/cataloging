<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Subscriber;

use App\Entity\CategoryEntity;
use App\Service\SlugService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

/**
 * Provides the category slug policy subscriber implementation.
 */
final readonly class CategorySlugPolicySubscriber implements EventSubscriber
{
    /**
     * Initializes the category slug policy subscriber service collaborators.
     */
    public function __construct(private SlugService $slugService)
    {
    }

    /**
     * Returns the subscribed events value.
     */
    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }

    /**
     * Handles the pre persist workflow.
     *
     * @throws \Throwable
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof CategoryEntity) {
            return;
        }
        $entity->setSlug($this->slugService->ensureUnique($entity->getSlug()));
    }

    /**
     * Handles the pre update workflow.
     *
     * @throws \Throwable
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof CategoryEntity) {
            return;
        }
        if ($args->hasChangedField('slug')) {
            $newSlug = $args->getNewValue('slug');
            $entity->setSlug($this->slugService->ensureUnique(is_string($newSlug) ? $newSlug : $entity->getSlug()));
        }
    }
}
