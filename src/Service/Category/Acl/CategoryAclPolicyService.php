<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service\Category\Acl;

use App\Cataloging\ServiceInterface\Acl\AclRepositoryInterface;
use App\Cataloging\ServiceInterface\Category\CategoryAclPolicyServiceInterface;

/**
 * Provides the category acl policy service application service.
 */
final class CategoryAclPolicyService implements CategoryAclPolicyServiceInterface
{
    private AclRepositoryInterface $repo;

    /**
     * Initializes the category acl policy service service collaborators.
     */
    public function __construct(AclRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

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
        return $this->repo->decide($subject);
    }
}
