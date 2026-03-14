<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Security\Category;

use App\ServiceInterface\Security\Category\AclRepositoryInterface;

class AclPolicyService
{
    public function __construct(private readonly AclRepositoryInterface $repo)
    {
    }

    public function allow(array $subject): bool
    {
        return $this->repo->decide($subject);
    }
}
