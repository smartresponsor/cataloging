<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Service layer — transactional operations for tests.
 */

namespace App\Service;

use App\Service\Catalogtests\Domain\tests;
use App\Service\Catalogtests\Repository\testsRepository;

final class CatalogtestsService
{
    public function __construct(private testsRepository $repo)
    {
    }

    public function create(tests $category): tests
    {
        $this->repo->save($category);

        return $category;
    }

    public function update(tests $category): tests
    {
        $this->repo->save($category);

        return $category;
    }

    public function move(string $id, ?string $newParentId): tests
    {
        $this->repo->move($id, $newParentId);
        $updated = $this->repo->getById($id);
        if (!$updated) {
            throw new \RuntimeException('tests not found after move');
        }

        return $updated;
    }
}
