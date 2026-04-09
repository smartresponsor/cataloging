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
    public function __construct(private SlugService $svc)
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
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        $e = $args->getObject();
        if (!$e instanceof CategoryEntity) {
            return;
        }
        $e->setSlug($this->svc->ensureUnique($e->getSlug()));
    }

    /**
     * Handles the pre update workflow.
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $e = $args->getObject();
        if (!$e instanceof CategoryEntity) {
            return;
        }
        if ($args->hasChangedField('slug')) {
            $newSlug = $args->getNewValue('slug');
            $e->setSlug($this->svc->ensureUnique(is_string($newSlug) ? $newSlug : $e->getSlug()));
        }
    }
}
