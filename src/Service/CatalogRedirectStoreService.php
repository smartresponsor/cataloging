<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\CatalogSeoRedirectEntity;
use App\Cataloging\ServiceInterface\CatalogRedirectStoreServiceInterface;
use App\Cataloging\ValueObject\RedirectPutRequest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the redirect store application service.
 */
final readonly class CatalogRedirectStoreService implements CatalogRedirectStoreServiceInterface
{
    /**
     * Initializes the redirect store service collaborators.
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Handles the put workflow.
     */
    public function put(RedirectPutRequest $request): void
    {
        /** @var CatalogSeoRedirectEntity|null $entity */
        $entity = $this->entityManager->find(CatalogSeoRedirectEntity::class, $request->from());
        if (!$entity instanceof CatalogSeoRedirectEntity) {
            $entity = new CatalogSeoRedirectEntity($request->from(), $request->to(), $request->status());
            $this->entityManager->persist($entity);
        } else {
            $entity->changeToPath($request->to());
            $entity->changeStatus($request->status());
        }

        $this->entityManager->flush();
    }

    /** @return array{from:string,to:string,status:int}|null */
    public function get(string $from): ?array
    {
        /** @var CatalogSeoRedirectEntity|null $entity */
        $entity = $this->entityManager->find(CatalogSeoRedirectEntity::class, $from);
        if (!$entity instanceof CatalogSeoRedirectEntity) {
            return null;
        }

        return [
            'from' => $entity->fromPath(),
            'to' => $entity->toPath(),
            'status' => $entity->status(),
        ];
    }
}
