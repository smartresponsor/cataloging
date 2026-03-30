<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Tests\Category;

use App\RepositoryInterface\CatalogAttachmentRepositoryInterface;
use App\Service\AttachmentService;
use PHPUnit\Framework\TestCase;

final class AttachmentServiceTest extends TestCase
{
    public function testListNormalizesBlankCategoryIdToNull(): void
    {
        $repository = new class implements CatalogAttachmentRepositoryInterface {
            public ?string $categoryId = 'sentinel';

            public function list(?string $categoryId = null): array
            {
                $this->categoryId = $categoryId;

                return [];
            }

            public function add(string $categoryId, string $type, string $path): array
            {
                return [
                    'attachment_id' => '01HZZZZZZZZZZZZZZZZZZZZZZZ',
                    'category_id' => $categoryId,
                    'type' => $type,
                    'path' => $path,
                    'created_at' => '2026-03-29T00:00:00+00:00',
                ];
            }
        };

        $service = new AttachmentService($repository);
        $service->list('   ');

        self::assertNull($repository->categoryId);
    }

    public function testAddTrimsAndDelegatesToRepository(): void
    {
        $repository = new class implements CatalogAttachmentRepositoryInterface {
            public array $payload = [];

            public function list(?string $categoryId = null): array
            {
                return [];
            }

            public function add(string $categoryId, string $type, string $path): array
            {
                $this->payload = [
                    'categoryId' => $categoryId,
                    'type' => $type,
                    'path' => $path,
                ];

                return [
                    'attachment_id' => '01HZZZZZZZZZZZZZZZZZZZZZZZ',
                    'category_id' => $categoryId,
                    'type' => $type,
                    'path' => $path,
                    'created_at' => '2026-03-29T00:00:00+00:00',
                ];
            }
        };

        $service = new AttachmentService($repository);
        $item = $service->add(' cat-1 ', ' icon ', ' /assets/icon.png ');

        self::assertSame([
            'categoryId' => 'cat-1',
            'type' => 'icon',
            'path' => '/assets/icon.png',
        ], $repository->payload);
        self::assertSame('cat-1', $item['category_id']);
        self::assertSame('icon', $item['type']);
        self::assertSame('/assets/icon.png', $item['path']);
    }

    public function testAddRejectsBlankCategoryId(): void
    {
        $repository = $this->createMock(CatalogAttachmentRepositoryInterface::class);
        $service = new AttachmentService($repository);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('category_id is required');

        $service->add('   ', 'icon', '/assets/icon.png');
    }
}
