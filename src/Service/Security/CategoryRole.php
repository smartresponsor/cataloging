<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Security;

final class CategoryRole
{
    public const OWNER = 'category.owner';
    public const EDITOR = 'category.editor';
    public const PUBLISHER = 'category.publisher';
    public const READER = 'category.reader';
    public const AUDITOR = 'category.auditor';
}
