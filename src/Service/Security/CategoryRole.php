<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Security;
/**
 * Provides the category role application service.
 */
final class CategoryRole
{
public const string OWNER = 'category.owner';
public const string EDITOR = 'category.editor';
public const string PUBLISHER = 'category.publisher';
public const string READER = 'category.reader';
public const string AUDITOR = 'category.auditor';
}
