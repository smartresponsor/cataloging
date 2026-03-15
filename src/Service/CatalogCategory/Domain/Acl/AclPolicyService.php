<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\CatalogCategory\Domain\Acl;

final class AclPolicyService
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
