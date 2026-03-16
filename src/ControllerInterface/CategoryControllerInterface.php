<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ControllerInterface;

interface CategoryControllerInterface
{
    /** GET /category/{taxonomy}/tree?locale=en&depth=2&parentId=ULID */
    public function tree(array $query, array $route): array;

    /** GET /category/{taxonomy}/by-slug/{slug}?locale=en */
    public function bySlug(array $query, array $route): array;

    /** POST /category/{taxonomy} */
    public function create(array $body, array $route, array $auth): array;

    /** PATCH /category/{id} */
    public function move(array $body, array $route, array $auth): array;

    /** POST /category/{id}/attach */
    public function attach(array $body, array $route, array $auth): array;

    /** POST /category/{id}/detach */
    public function detach(array $body, array $route, array $auth): array;
}
