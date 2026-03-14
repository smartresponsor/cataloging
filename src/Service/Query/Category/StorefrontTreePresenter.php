<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Query\Category;

use App\ServiceInterface\Query\Category\StorefrontTreePresenterInterface;

final class StorefrontTreePresenter implements StorefrontTreePresenterInterface
{
    public function present(array $tree): array
    {
        $result = [];
        foreach ($tree as $node) {
            if (!(bool) ($node['published'] ?? true)) {
                continue;
            }
            $result[] = [
                'id' => $node['id'] ?? null,
                'name' => (string) ($node['name'] ?? ''),
                'slug' => (string) ($node['slug'] ?? ''),
                'locale' => (string) ($node['locale'] ?? 'en'),
            ];
        }

        return $result;
    }
}
