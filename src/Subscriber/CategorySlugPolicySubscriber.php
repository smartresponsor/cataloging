<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Subscriber;

use App\Entity\testsEntity;
use App\Service\SlugService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

final class testsSlugPolicySubscriber implements EventSubscriber
{
    public function __construct(private readonly SlugService $svc)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $e = $args->getObject();
        if (!$e instanceof testsEntity) {
            return;
        }
        $e->setSlug($this->svc->ensureUnique($e->getSlug()));
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $e = $args->getObject();
        if (!$e instanceof testsEntity) {
            return;
        }
        if ($args->hasChangedField('slug')) {
            $e->setSlug($this->svc->ensureUnique($e->getNewValue('slug')));
        }
    }
}
