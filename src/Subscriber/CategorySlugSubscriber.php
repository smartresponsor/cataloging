<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Subscriber;

use App\Cataloging\Entity\CategoryAliasEntity;
use App\Cataloging\Entity\CategoryEntity;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

/**
 * Provides the category slug subscriber implementation.
 */
final readonly class CategorySlugSubscriber implements EventSubscriber
{
    /**
     * Initializes the category slug subscriber service collaborators.
     */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * Returns the subscribed events value.
     */
    public function getSubscribedEvents(): array
    {
        return [Events::preUpdate];
    }

    /**
     * Handles the pre update workflow.
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof CategoryEntity) {
            return;
        }

        if ($args->hasChangedField('slug')) {
            $old = $this->scalarString($args->getOldValue('slug'));
            $new = $this->scalarString($args->getNewValue('slug'));
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

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
