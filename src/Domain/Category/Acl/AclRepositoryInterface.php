<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\Category\Domain\Acl;

interface AclRepositoryInterface
{
    /**
     * Put ACL rule for a category.
     *
     * @param array{categoryId:string, tenantId:string, storeId?:string|null, role:string, principalId?:string|null, visible:bool, locale?:string|null, validFrom?:string|null, validTo?:string|null} $rule
     */
    public function put(array $rule): void;

    /**
     * @return list<array{role:string, visible:bool, locale?:string|null}>
     */
    public function list(array $filter): array;

    /**
     * Decision for a subject.
     *
     * @param array{categoryId:string, tenantId:string, storeId?:string|null, role:string, principalId?:string|null, locale?:string|null, at?:string|null} $input
     */
    public function decide(array $input): bool;
}
