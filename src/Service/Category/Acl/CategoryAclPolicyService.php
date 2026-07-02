<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Category\Acl;

use App\Cataloging\ServiceInterface\Category\CategoryAclPolicyServiceInterface;

/**
 * Provides a Cataloging-side category access policy seam without owning ACL persistence.
 */
final class CategoryAclPolicyService implements CategoryAclPolicyServiceInterface
{
    /**
     * @param array{
     *     categoryId:string,
     *     tenantId:string,
     *     storeId?:string|null,
     *     role:string,
     *     principalId?:string|null,
     *     locale?:string|null,
     * } $subject
     */
    public function allow(array $subject): bool
    {
        unset($subject);

        return false;
    }
}
