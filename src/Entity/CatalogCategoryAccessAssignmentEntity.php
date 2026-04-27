<?php

declare(strict_types=1);

namespace App\Cataloging\Entity;

use App\Cataloging\EntityInterface\Catalog\CatalogCategoryAccessAssignmentEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;

#[ORM\Entity]
#[ORM\Table(name: 'category_access_assignment')]
#[ORM\UniqueConstraint(name: 'uniq_category_access_assignment_actor', columns: ['category_id', 'actor_user_id'])]
#[ORM\Index(name: 'idx_category_access_assignment_category_status', columns: ['category_id', 'status'])]
#[ORM\Index(name: 'idx_category_access_assignment_actor_status', columns: ['actor_user_id', 'status'])]
final class CatalogCategoryAccessAssignmentEntity implements CatalogCategoryAccessAssignmentEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'assignment_id', type: 'string', length: 32)]
    private string $assignmentId;

    #[ORM\Column(name: 'category_id', type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(name: 'actor_user_id', type: 'string', length: 190)]
    private string $actorUserId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $role;

    #[ORM\Column(type: 'string', length: 32)]
    private string $status;

    #[ORM\Column(name: 'is_primary', type: 'boolean')]
    private bool $isPrimary;

    #[ORM\Column(name: 'granted_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $grantedAt;

    #[ORM\Column(name: 'revoked_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $revokedAt;

    public function __construct(
        string $assignmentId,
        string $categoryId,
        string $actorUserId,
        string $role,
        string $status,
        bool $isPrimary,
        \DateTimeImmutable $grantedAt,
        ?\DateTimeImmutable $revokedAt,
    ) {
        $this->assignmentId = $assignmentId;
        $this->categoryId = $categoryId;
        $this->actorUserId = $actorUserId;
        $this->role = $role;
        $this->status = $status;
        $this->isPrimary = $isPrimary;
        $this->grantedAt = $grantedAt;
        $this->revokedAt = $revokedAt;
    }

    /** @throws RandomException */
    public static function create(string $categoryId, string $actorUserId, string $role, bool $isPrimary = false): self
    {
        return new self(
            assignmentId: bin2hex(random_bytes(16)),
            categoryId: trim($categoryId),
            actorUserId: trim($actorUserId),
            role: trim($role),
            status: 'active',
            isPrimary: $isPrimary,
            grantedAt: new \DateTimeImmutable('now'),
            revokedAt: null,
        );
    }

    public function assignmentId(): string
    {
        return $this->assignmentId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function actorUserId(): string
    {
        return $this->actorUserId;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function grantedAt(): \DateTimeImmutable
    {
        return $this->grantedAt;
    }

    public function revokedAt(): ?\DateTimeImmutable
    {
        return $this->revokedAt;
    }

    public function activate(): void
    {
        $this->status = 'active';
        $this->revokedAt = null;
    }

    public function revoke(): void
    {
        $this->status = 'revoked';
        $this->isPrimary = false;
        $this->revokedAt = new \DateTimeImmutable('now');
    }

    public function markPrimary(): void
    {
        $this->isPrimary = true;
    }

    public function clearPrimary(): void
    {
        $this->isPrimary = false;
    }

    public function changeRole(string $role): void
    {
        $this->role = trim($role);
    }
}
