<?php

declare(strict_types=1);

namespace App\Cataloging\Entity\Catalog;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryWorkflowEntityInterface;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use App\Cataloging\ValueObjectInterface\CatalogCategoryWorkflowEntityStateInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category_workflow')]
final class CatalogCategoryWorkflowEntity implements CatalogCategoryWorkflowEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'category_id', type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(name: 'state_value', type: 'string', length: 32)]
    private string $stateValue;

    #[ORM\Column(name: 'actor_id', type: 'string', length: 190)]
    private string $actorId;

    #[ORM\Column(type: 'text')]
    private string $reason;

    #[ORM\Column(name: 'transitioned_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $transitionedAt;

    public function __construct(string $categoryId, CatalogCategoryWorkflowEntityState $state, string $actorId, string $reason, \DateTimeImmutable $transitionedAt)
    {
        $this->categoryId = $categoryId;
        $this->stateValue = $state->value();
        $this->actorId = $actorId;
        $this->reason = $reason;
        $this->transitionedAt = $transitionedAt;
    }

    public static function initialize(string $categoryId, string $actorId): self
    {
        return new self($categoryId, CatalogCategoryWorkflowEntityState::draft(), $actorId, 'workflow initialized', new \DateTimeImmutable('now'));
    }

    public function transitionTo(CatalogCategoryWorkflowEntityState $state, string $actorId, string $reason): self
    {
        return new self($this->categoryId, $state, $actorId, $reason, new \DateTimeImmutable('now'));
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function state(): CatalogCategoryWorkflowEntityStateInterface
    {
        return CatalogCategoryWorkflowEntityState::fromString($this->stateValue);
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function transitionedAt(): \DateTimeImmutable
    {
        return $this->transitionedAt;
    }
}
