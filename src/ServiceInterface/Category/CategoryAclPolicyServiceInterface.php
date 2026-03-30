<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface\Category;

interface CategoryAclPolicyServiceInterface
{
    /** @param array{categoryId:string, tenantId:string, storeId?:string|null, role:string, principalId?:string|null, locale?:string|null} $subject */
    public function allow(array $subject): bool;
}
