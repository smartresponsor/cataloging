<?php

declare(strict_types=1);

namespace App\Tests\Request;

use App\Request\CatalogCategoryMoveRequest;
use App\Request\CatalogCategoryPublishRequest;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryRequestTest extends TestCase
{
    public function testMoveRequestParsesDefaultsAndBooleans(): void
    {
        $request = CatalogCategoryMoveRequest::fromArray([
            'parent_id' => ' parent ',
            'tree_id' => '',
            'policy' => '',
            'dry_run' => 'true',
            'locale' => ' en ',
        ]);

        self::assertTrue($request->isValid());
        self::assertSame('parent', $request->parentId);
        self::assertSame('catalog', $request->treeId);
        self::assertSame('strict', $request->policy);
        self::assertTrue($request->dryRun);
        self::assertSame('en', $request->locale);
    }

    public function testMoveRequestRequiresParentId(): void
    {
        $request = CatalogCategoryMoveRequest::fromArray([]);

        self::assertFalse($request->isValid());
        self::assertSame(['parent_id is required'], $request->getErrors());
    }

    public function testMoveRequestRejectsUnsupportedPolicy(): void
    {
        $request = CatalogCategoryMoveRequest::fromArray([
            'parent_id' => 'parent',
            'policy' => 'lenient',
        ]);

        self::assertFalse($request->isValid());
        self::assertSame(['policy must be one of: strict'], $request->getErrors());
    }

    public function testPublishRequestRequiresChecksOnPublish(): void
    {
        $request = CatalogCategoryPublishRequest::fromArray(['published' => true]);

        self::assertFalse($request->isValid());
        self::assertSame(['checks are required when published is true'], $request->getErrors());
    }

    public function testPublishRequestParsesBooleanChecksAndReason(): void
    {
        $request = CatalogCategoryPublishRequest::fromArray([
            'published' => '1',
            'checks' => ['slugReady' => 1, 'contentReady' => true],
            'reason' => ' manual ',
        ]);

        self::assertTrue($request->isValid());
        self::assertTrue($request->published);
        self::assertSame(['slugReady' => true, 'contentReady' => true], $request->checks);
        self::assertSame('manual', $request->reason);
    }
}
