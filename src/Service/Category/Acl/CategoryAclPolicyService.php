<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Acl;

use App\ServiceInterface\Acl\AclRepositoryInterface;
use App\ServiceInterface\Category\CategoryAclPolicyServiceInterface;

final class CategoryAclPolicyService implements CategoryAclPolicyServiceInterface
{
    private AclRepositoryInterface $repo;

    public function __construct(AclRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param array{categoryId:string, tenantId:string, storeId?:string|null, role:string, principalId?:string|null, locale?:string|null} $subject
     */
    public function allow(array $subject): bool
    {
        return $this->repo->decide($subject);
    }
}
