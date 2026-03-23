<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Repository;

use App\EntityInterface\CategoryChangeRequestInterface;
use App\EventInterface\CategoryChangeRequestReviewedInterface;
use App\RepositoryInterface\CategoryChangeRequestRepositoryInterface;

final class CategoryChangeRequestRepository implements CategoryChangeRequestRepositoryInterface
{
    /** @var array<string,CategoryChangeRequestInterface> */
    private array $requests = [];

    /** @var array<string,list<CategoryChangeRequestReviewedInterface>> */
    private array $reviewHistory = [];

    public function findByRequestId(string $requestId): ?CategoryChangeRequestInterface
    {
        return $this->requests[$requestId] ?? null;
    }

    public function save(CategoryChangeRequestInterface $request): void
    {
        $this->requests[$request->requestId()] = $request;
    }

    public function appendReviewHistory(CategoryChangeRequestReviewedInterface $event): void
    {
        $payload = $event->payload();
        $requestId = (string) ($payload['requestId'] ?? '');
        $this->reviewHistory[$requestId] ??= [];
        $this->reviewHistory[$requestId][] = $event;
    }

    public function reviewHistoryForRequestId(string $requestId): array
    {
        return $this->reviewHistory[$requestId] ?? [];
    }
}
