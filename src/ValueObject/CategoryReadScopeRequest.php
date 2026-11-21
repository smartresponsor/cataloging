<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use Symfony\Component\HttpFoundation\Request;

/**
 * Carries the full input surface for category read scope coordination workflows.
 */
final readonly class CategoryReadScopeRequest
{
    public function __construct(
        private Request $request,
        private CategoryProjectionCriteria $criteria,
    ) {
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function criteria(): CategoryProjectionCriteria
    {
        return $this->criteria;
    }
}
