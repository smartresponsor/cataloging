<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category;

use App\ServiceInterface\Category\CategoryBulkInterface;
use App\ServiceInterface\Category\CategoryInterface as CategoryService;

final class CategoryBulk implements CategoryBulkInterface
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function execute(string $actorId, string $batchKey, array $ops): array
    {
        $accepted = 0;
        $rejected = 0;
        $results = [];
        foreach ($ops as $op) {
            try {
                switch ($op['op']) {
                    case 'create':
                        $p = $op['payload'];
                        $results[] = $this->service->create($actorId, (string) $p['taxonomyId'], $p['parentId'] ?? null, (array) $p['name'], (array) $p['slug'], (array) ($p['meta'] ?? []));
                        ++$accepted;
                        break;
                    case 'move':
                        $p = $op['payload'];
                        $results[] = $this->service->move($actorId, (string) $p['id'], $p['parentId'] ?? null, (int) ($p['order'] ?? 0));
                        ++$accepted;
                        break;
                    case 'attach':
                        $p = $op['payload'];
                        $this->service->attach($actorId, (string) $p['id'], (string) $p['targetDomain'], (string) $p['targetClass'], (string) $p['targetId']);
                        ++$accepted;
                        break;
                    case 'detach':
                        $p = $op['payload'];
                        $this->service->detach($actorId, (string) $p['id'], (string) $p['targetDomain'], (string) $p['targetClass'], (string) $p['targetId']);
                        ++$accepted;
                        break;
                    default:
                        $rejected++;
                        $results[] = ['error' => 'Unknown op'];
                }
            } catch (\Throwable $e) {
                ++$rejected;
                $results[] = ['error' => $e->getMessage()];
            }
        }

        return ['accepted' => $accepted, 'rejected' => $rejected, 'results' => $results];
    }
}
