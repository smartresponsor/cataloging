<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Rule;

final class CategoryRulePolicy
{
    public const MAX_CONDITIONS = 20;
    public const PRICE_MIN = 0;
    public const PRICE_MAX = 1000000;
    /** @var string[] */
    public static array $allowedAttrs = ['brand', 'price', 'stock'];
    /** @var string[] */
    public static array $allowedOps = ['in', 'between', '>', '>=', '<', '<='];
}
