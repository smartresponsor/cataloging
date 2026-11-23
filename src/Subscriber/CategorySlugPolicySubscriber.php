<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Subscriber;

use App\Cataloging\Entity\CategoryEntity;
use App\Cataloging\Service\SlugService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

final class CategorySlugPolicySubscriber implements EventSubscriber
{
    public function __construct(private readonly SlugService $svc)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $e = $args->getObject();
        if (!$e instanceof CategoryEntity) {
            return;
        }
        $e->setSlug($this->svc->ensureUnique($e->getSlug()));
    }

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
