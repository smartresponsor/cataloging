<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ValueObjectInterface;

interface CategoryReviewDecisionCouplingResultInterface
{
    public function requestId(): string;

    public function categoryId(): string;

    public function reviewState(): string;

    public function workflowState(): string;

    public function publishable(): bool;

    /** @return list<string> */
    public function blockers(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;

    public function actorId(): string;

    public function reason(): string;

    /** @return array<string,mixed> */
    public function payload(): array;
}
