<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryReviewAssignmentEntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category_review_assignment')]
#[ORM\Index(name: 'idx_category_review_assignment_reviewer', columns: ['assigned_reviewer'])]
#[ORM\Index(name: 'idx_category_review_assignment_category', columns: ['category_id'])]
final class CatalogCategoryReviewAssignmentEntity implements CatalogCategoryReviewAssignmentEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'request_id', type: 'string', length: 64)]
    private string $requestId;

    #[ORM\Column(name: 'category_id', type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(name: 'assigned_reviewer', type: 'string', length: 190)]
    private string $assignedReviewer;

    #[ORM\Column(name: 'assigned_by', type: 'string', length: 190)]
    private string $assignedBy;

    #[ORM\Column(type: 'string', length: 32)]
    private string $priority;

    #[ORM\Column(name: 'assigned_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $assignedAt;

    #[ORM\Column(name: 'due_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dueAt;

    public function __construct(string $requestId, string $categoryId, string $assignedReviewer, string $assignedBy, string $priority, \DateTimeImmutable $assignedAt, ?\DateTimeImmutable $dueAt)
    {
        $this->requestId = $requestId;
        $this->categoryId = $categoryId;
        $this->assignedReviewer = $assignedReviewer;
        $this->assignedBy = $assignedBy;
        $this->priority = $priority;
        $this->assignedAt = $assignedAt;
        $this->dueAt = $dueAt;
    }

    public static function create(string $requestId, string $categoryId, string $assignedReviewer, string $assignedBy, string $priority, ?\DateTimeImmutable $dueAt): self
    {
        return new self(trim($requestId), trim($categoryId), trim($assignedReviewer), trim($assignedBy), trim($priority), new \DateTimeImmutable('now'), $dueAt);
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function assignedReviewer(): string
    {
        return $this->assignedReviewer;
    }

    public function assignedBy(): string
    {
        return $this->assignedBy;
    }

    public function priority(): string
    {
        return $this->priority;
    }

    public function assignedAt(): \DateTimeImmutable
    {
        return $this->assignedAt;
    }

    public function dueAt(): ?\DateTimeImmutable
    {
        return $this->dueAt;
    }
}
