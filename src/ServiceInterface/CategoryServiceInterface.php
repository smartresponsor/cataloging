<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\ValueObject\CategoryCreateRequest;
use App\ValueObject\CategoryLinkRequest;
use App\ValueObject\CategoryResolveRequest;
use App\ValueObject\CategoryServiceMoveRequest;

/**
 * Defines the contract for category service.
 */
interface CategoryServiceInterface
{
    /** @return array<string,mixed> */
    public function create(CategoryCreateRequest $request): array;

    /** @return array<string,mixed> */
    public function move(CategoryServiceMoveRequest $request): array;

    /**
     * Handles the attach workflow.
     */
    public function attach(CategoryLinkRequest $request): void;

    /**
     * Handles the detach workflow.
     */
    public function detach(CategoryLinkRequest $request): void;

    /** @return list<array<string,mixed>> */
    public function resolve(CategoryResolveRequest $request): array;
}
