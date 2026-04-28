<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogOutboxMessageEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class CatalogOutboxWriterService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /** @param array<string,mixed> $payload */
    public function append(string $type, array $payload, string $key): void
    {
        $createdAtDateTime = new \DateTimeImmutable('now');
        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $repository = $this->entityManager->getRepository(CatalogOutboxMessageEntity::class);
        $existing = $repository->findOneBy(['messageKey' => $key]);
        if ($existing instanceof CatalogOutboxMessageEntity) {
            return;
        }

        $entity = new CatalogOutboxMessageEntity(Uuid::v7()->toRfc4122(), $type, $payloadJson, $key, $createdAtDateTime);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
