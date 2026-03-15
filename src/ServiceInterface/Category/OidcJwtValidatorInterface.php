<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace LayerInterface\Category;

interface OidcJwtValidatorInterface
{
    public function validate(string $jwt): array;
}
