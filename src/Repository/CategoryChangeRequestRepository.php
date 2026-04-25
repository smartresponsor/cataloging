<?php

declare(strict_types=1);

namespace App\Cataloging\Repository;

use App\Cataloging\Entity\CatalogCategoryChangeRequestEntity;
use App\Cataloging\EntityInterface\CategoryChangeRequestInterface;
use App\Cataloging\EventInterface\CategoryChangeRequestReviewedInterface;
use App\Cataloging\RepositoryInterface\CategoryChangeRequestRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CategoryChangeRequestRepository implements CategoryChangeRequestRepositoryInterface
{
    /** @var array<string,CategoryChangeRequestInterface> */
    private array $requests = [];

    /** @var array<string,list<CategoryChangeRequestReviewedInterface>> */
    private array $reviewHistory = [];

    public function __construct(private readonly ?EntityManagerInterface $entityManager = null)
    {
    }

    public function findByRequestId(string $requestId): ?CategoryChangeRequestInterface
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->find(CatalogCategoryChangeRequestEntity::class, trim($requestId));
        }

        return $this->requests[$requestId] ?? null;
    }

    public function findByCategoryId(string $categoryId): array
    {
        if ($this->entityManager instanceof EntityManagerInterface) {
            return $this->entityManager->getRepository(CatalogCategoryChangeRequestEntity::class)->findBy(['categoryId' => trim($categoryId)]);
        }

        return array_values(array_filter(
            $this->requests,
            static fn (CategoryChangeRequestInterface $request): bool => $request->categoryId() === $categoryId,
        ));
    }

    public function save(CategoryChangeRequestInterface $request): void
    {
        if ($this->entityManager instanceof EntityManagerInterface && $request instanceof CatalogCategoryChangeRequestEntity) {
            $this->entityManager->persist($request);
            $this->entityManager->flush();

            return;
        }

        $this->requests[$request->requestId()] = $request;
    }

    public function appendReviewHistory(CategoryChangeRequestReviewedInterface $event): void
    {
        $requestId = trim($event->requestId());
        $this->reviewHistory[$requestId] ??= [];
        $this->reviewHistory[$requestId][] = $event;
    }

    public function reviewHistoryForRequestId(string $requestId): array
    {
        return $this->reviewHistory[trim($requestId)] ?? [];
    }
}
