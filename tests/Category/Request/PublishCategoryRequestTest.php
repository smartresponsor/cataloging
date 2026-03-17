<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Request;

use App\Request\PublishCategoryRequest;
use PHPUnit\Framework\TestCase;

final class PublishCategoryRequestTest extends TestCase
{
    public function testFromArrayIsValidWhenPublishedFlagExists(): void
    {
        $request = PublishCategoryRequest::fromArray(['published' => true]);

        self::assertTrue($request->isValid());
        self::assertSame([], $request->getErrors());
        self::assertTrue($request->published);
    }

    public function testFromArrayCollectsErrorWhenPublishedFlagMissing(): void
    {
        $request = PublishCategoryRequest::fromArray([]);

        self::assertFalse($request->isValid());
        self::assertSame(['published is required'], $request->getErrors());
        self::assertNull($request->published);
    }

    public function testFromArrayRejectsNonBooleanPublishedFlag(): void
    {
        $request = PublishCategoryRequest::fromArray(['published' => 'yes']);

        self::assertFalse($request->isValid());
        self::assertSame(['published must be boolean'], $request->getErrors());
    }
}
