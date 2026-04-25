<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EntityInterface;

use App\Cataloging\ValueObjectInterface\CategoryMediaRoleInterface;

/**
 * Defines the contract for category media binding.
 */
interface CatalogCategoryMediaBindingEntityInterface
{
    /**
     * Handles the binding id workflow.
     */
    public function bindingId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the asset id workflow.
     */
    public function assetId(): string;

    /**
     * Handles the role workflow.
     */
    public function role(): CategoryMediaRoleInterface;

    /** @return list<string> */
    public function channels(): array;

    /** @return list<string> */
    public function locales(): array;

    /**
     * Handles the required for publish workflow.
     */
    public function requiredForPublish(): bool;

    /**
     * Handles the active workflow.
     */
    public function active(): bool;

    /** @return array<string,mixed> */
    public function metadata(): array;

    /**
     * Handles the actor id workflow.
     */
    public function actorId(): string;

    /**
     * Handles the bound at workflow.
     */
    public function boundAt(): \DateTimeImmutable;
}
if (!class_exists(__NAMESPACE__.'\\CategoryMediaBindingInterface', false)) {
    class_alias(CatalogCategoryMediaBindingEntityInterface::class, __NAMESPACE__.'\\CategoryMediaBindingInterface');
}
