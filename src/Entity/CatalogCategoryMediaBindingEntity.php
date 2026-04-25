<?php

declare(strict_types=1);

namespace App\Cataloging\Entity;

use App\Cataloging\EntityInterface\CatalogCategoryMediaBindingEntityInterface;
use App\Cataloging\ValueObject\CategoryMediaRole;
use App\Cataloging\ValueObjectInterface\CategoryMediaRoleInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'category_media_binding')]
#[ORM\Index(name: 'idx_category_media_binding_category_active', columns: ['category_id', 'active'])]
final class CatalogCategoryMediaBindingEntity implements CatalogCategoryMediaBindingEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'binding_id', type: 'string', length: 64)]
    private string $bindingId;

    #[ORM\Column(name: 'category_id', type: 'string', length: 26)]
    private string $categoryId;

    #[ORM\Column(name: 'asset_id', type: 'string', length: 190)]
    private string $assetId;

    #[ORM\Column(name: 'role_name', type: 'string', length: 32)]
    private string $roleName;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $channels;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $locales;

    #[ORM\Column(name: 'required_for_publish', type: 'boolean')]
    private bool $requiredForPublish;

    #[ORM\Column(type: 'boolean')]
    private bool $active;

    /** @var array<string,mixed> */
    #[ORM\Column(type: 'json')]
    private array $metadata;

    #[ORM\Column(name: 'actor_id', type: 'string', length: 190)]
    private string $actorId;

    #[ORM\Column(name: 'bound_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $boundAt;

    /** @param list<string> $channels @param list<string> $locales @param array<string,mixed> $metadata */
    public function __construct(string $bindingId, string $categoryId, string $assetId, CategoryMediaRole $role, array $channels, array $locales, bool $requiredForPublish, bool $active, array $metadata, string $actorId, \DateTimeImmutable $boundAt)
    {
        $this->bindingId = $bindingId;
        $this->categoryId = $categoryId;
        $this->assetId = $assetId;
        $this->roleName = $role->value();
        $this->channels = $channels;
        $this->locales = $locales;
        $this->requiredForPublish = $requiredForPublish;
        $this->active = $active;
        $this->metadata = $metadata;
        $this->actorId = $actorId;
        $this->boundAt = $boundAt;
    }

    public function bindingId(): string
    {
        return $this->bindingId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function assetId(): string
    {
        return $this->assetId;
    }

    public function role(): CategoryMediaRoleInterface
    {
        return CategoryMediaRole::fromString($this->roleName);
    }

    public function channels(): array
    {
        return $this->channels;
    }

    public function locales(): array
    {
        return $this->locales;
    }

    public function requiredForPublish(): bool
    {
        return $this->requiredForPublish;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function boundAt(): \DateTimeImmutable
    {
        return $this->boundAt;
    }
}
