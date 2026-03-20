<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
declare(strict_types=1);

namespace App\ServiceInterface;

interface OidcJwtValidatorInterface
{
    public function validate(string $jwt): array;
}
