<?php

declare(strict_types=1);

namespace App\Cataloging\Entity;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryChangeRequestEntityInterface;
use App\Cataloging\ValueObject\CategoryChangeRequestState;
use App\Cataloging\ValueObjectInterface\CategoryChangeRequestStateInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category_change_request')]
#[ORM\Index(name: 'idx_category_change_request_category_state', columns: ['category_id', 'state'])]
final class CatalogCategoryChangeRequestEntity implements CatalogCategoryChangeRequestEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'request_id', type: 'string', length: 64)]
    private string $requestId;

    #[ORM\Column(name: 'category_id', type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(name: 'submitted_by', type: 'string', length: 190)]
    private string $submittedBy;

    #[ORM\Column(type: 'string', length: 500)]
    private string $summary;

    /** @var array<string,mixed> */
    #[ORM\Column(type: 'json')]
    private array $changes;

    #[ORM\Column(name: 'state', type: 'string', length: 32)]
    private string $stateValue;

    #[ORM\Column(name: 'reviewed_by', type: 'string', length: 190, nullable: true)]
    private ?string $reviewedBy;

    #[ORM\Column(name: 'decision_reason', type: 'text', nullable: true)]
    private ?string $decisionReason;

    #[ORM\Column(name: 'submitted_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $submittedAt;

    #[ORM\Column(name: 'reviewed_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $reviewedAt;

    /** @param array<mixed, mixed> $changes */
    public function __construct(
        string $requestId,
        string $categoryId,
        string $submittedBy,
        string $summary,
        array $changes,
        CategoryChangeRequestState $state,
        ?string $reviewedBy,
        ?string $decisionReason,
        \DateTimeImmutable $submittedAt,
        ?\DateTimeImmutable $reviewedAt,
    ) {
        $this->requestId = $requestId;
        $this->categoryId = $categoryId;
        $this->submittedBy = $submittedBy;
        $this->summary = $summary;
        $this->changes = self::normalizeStringKeyMap($changes);
        $this->stateValue = $state->value();
        $this->reviewedBy = $reviewedBy;
        $this->decisionReason = $decisionReason;
        $this->submittedAt = $submittedAt;
        $this->reviewedAt = $reviewedAt;
    }

    /** @param array<string,mixed> $changes */
    public static function open(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): self
    {
        return new self($requestId, $categoryId, $submittedBy, trim($summary), $changes, CategoryChangeRequestState::proposed(), null, null, new \DateTimeImmutable('now'), null);
    }

    public function moveTo(CategoryChangeRequestState $state, string $reviewedBy, string $decisionReason): self
    {
        return new self($this->requestId, $this->categoryId, $this->submittedBy, $this->summary, $this->changes, $state, trim($reviewedBy), trim($decisionReason), $this->submittedAt, new \DateTimeImmutable('now'));
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function submittedBy(): string
    {
        return $this->submittedBy;
    }

    public function summary(): string
    {
        return $this->summary;
    }

    /** @return array<string,mixed> */
    public function changes(): array
    {
        return $this->changes;
    }

    public function state(): CategoryChangeRequestStateInterface
    {
        return CategoryChangeRequestState::fromString($this->stateValue);
    }

    public function reviewedBy(): ?string
    {
        return $this->reviewedBy;
    }

    public function decisionReason(): ?string
    {
        return $this->decisionReason;
    }

    public function submittedAt(): \DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function reviewedAt(): ?\DateTimeImmutable
    {
        return $this->reviewedAt;
    }

    /**
     * @param array<mixed, mixed> $values
     *
     * @return array<string, mixed>
     */
    private static function normalizeStringKeyMap(array $values): array
    {
        $normalized = [];

        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }
}
