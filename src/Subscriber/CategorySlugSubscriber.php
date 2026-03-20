<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Subscriber;

use App\Entity\CategoryAliasEntity;
use App\Entity\CategoryEntity;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

final class CategorySlugSubscriber implements EventSubscriber
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::preUpdate];
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof CategoryEntity) {
            return;
        }

        if ($args->hasChangedField('slug')) {
            $old = (string) $args->getOldValue('slug');
            $new = (string) $args->getNewValue('slug');
            if ($old === $new) {
                return;
            }
            $repo = $this->em->getRepository(CategoryAliasEntity::class);
            if ($repo->findOneBy(['oldSlug' => $old])) {
                return;
            }
            $alias = new CategoryAliasEntity($old, $entity->getId());
            $this->em->persist($alias);
        }
    }
}
