<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Security;

final class CategoryRole
{
    public const OWNER = 'category.owner';
    public const EDITOR = 'category.editor';
    public const PUBLISHER = 'category.publisher';
    public const READER = 'category.reader';
    public const AUDITOR = 'category.auditor';
}
