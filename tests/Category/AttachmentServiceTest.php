<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Tests\Category;

use App\AttachmentInterface\AttachmentReferenceGatewayInterface;
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

            public function add(string $categoryId, string $type, string $provider, string $externalAttachmentId, ?string $referenceUri = null): array
            {
                return [
                    'attachment_id' => '01HZZZZZZZZZZZZZZZZZZZZZZZ',
                    'category_id' => $categoryId,
                    'type' => $type,
                    'provider' => $provider,
                    'external_attachment_id' => $externalAttachmentId,
                    'reference_uri' => $referenceUri,
                    'path' => $referenceUri,
                    'created_at' => '2026-03-29T00:00:00+00:00',
                ];
            }

            public function delete(string $attachmentId): bool
            {
                return false;
            }
        };

        $service = new AttachmentService($repository, new class implements AttachmentReferenceGatewayInterface {
            public function assertBindable(string $provider, string $externalAttachmentId, ?string $referenceUri = null): void
            {
            }
        });
        $service->list('   ');

        self::assertNull($repository->categoryId);
    }

    public function testAddTrimsAndDelegatesToRepository(): void
    {
        $repository = new class implements CatalogAttachmentRepositoryInterface {
            /** @var array{categoryId?:string,type?:string,provider?:string,externalAttachmentId?:string,referenceUri?:?string} */
            public array $payload = [];

            public function list(?string $categoryId = null): array
            {
                return [];
            }

            public function add(string $categoryId, string $type, string $provider, string $externalAttachmentId, ?string $referenceUri = null): array
            {
                $this->payload = [
                    'categoryId' => $categoryId,
                    'type' => $type,
                    'provider' => $provider,
                    'externalAttachmentId' => $externalAttachmentId,
                    'referenceUri' => $referenceUri,
                ];

                return [
                    'attachment_id' => '01HZZZZZZZZZZZZZZZZZZZZZZZ',
                    'category_id' => $categoryId,
                    'type' => $type,
                    'provider' => $provider,
                    'external_attachment_id' => $externalAttachmentId,
                    'reference_uri' => $referenceUri,
                    'path' => $referenceUri,
                    'created_at' => '2026-03-29T00:00:00+00:00',
                ];
            }

            public function delete(string $attachmentId): bool
            {
                return false;
            }
        };

        $gateway = new class implements AttachmentReferenceGatewayInterface {
            /** @var array{provider?:string,externalAttachmentId?:string,referenceUri?:?string} */
            public array $payload = [];

            public function assertBindable(string $provider, string $externalAttachmentId, ?string $referenceUri = null): void
            {
                $this->payload = [
                    'provider' => $provider,
                    'externalAttachmentId' => $externalAttachmentId,
                    'referenceUri' => $referenceUri,
                ];
            }
        };

        $service = new AttachmentService($repository, $gateway);
        $item = $service->add(' cat-1 ', ' icon ', ' media ', ' ext-42 ', ' https://cdn.example.test/icon.png ');

        self::assertSame([
            'provider' => 'media',
            'externalAttachmentId' => 'ext-42',
            'referenceUri' => 'https://cdn.example.test/icon.png',
        ], $gateway->payload);
        self::assertSame([
            'categoryId' => 'cat-1',
            'type' => 'icon',
            'provider' => 'media',
            'externalAttachmentId' => 'ext-42',
            'referenceUri' => 'https://cdn.example.test/icon.png',
        ], $repository->payload);
        self::assertSame('cat-1', $item['category_id']);
        self::assertSame('icon', $item['type']);
        self::assertSame('media', $item['provider']);
        self::assertSame('ext-42', $item['external_attachment_id']);
        self::assertSame('https://cdn.example.test/icon.png', $item['reference_uri']);
    }

    public function testAddRejectsBlankCategoryId(): void
    {
        $repository = $this->createMock(CatalogAttachmentRepositoryInterface::class);
        $gateway = $this->createMock(AttachmentReferenceGatewayInterface::class);
        $service = new AttachmentService($repository, $gateway);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('category_id is required');

        $service->add('   ', 'icon', 'media', 'ext-42', 'https://cdn.example.test/icon.png');
    }

    public function testRemoveTrimsAndDelegatesToRepository(): void
    {
        $repository = new class implements CatalogAttachmentRepositoryInterface {
            public ?string $attachmentId = null;

            public function list(?string $categoryId = null): array
            {
                return [];
            }

            public function add(string $categoryId, string $type, string $provider, string $externalAttachmentId, ?string $referenceUri = null): array
            {
                return [
                    'attachment_id' => '01HZZZZZZZZZZZZZZZZZZZZZZZ',
                    'category_id' => $categoryId,
                    'type' => $type,
                    'provider' => $provider,
                    'external_attachment_id' => $externalAttachmentId,
                    'reference_uri' => $referenceUri,
                    'path' => $referenceUri,
                    'created_at' => '2026-03-29T00:00:00+00:00',
                ];
            }

            public function delete(string $attachmentId): bool
            {
                $this->attachmentId = $attachmentId;

                return true;
            }
        };

        $service = new AttachmentService($repository, new class implements AttachmentReferenceGatewayInterface {
            public function assertBindable(string $provider, string $externalAttachmentId, ?string $referenceUri = null): void
            {
            }
        });

        self::assertTrue($service->remove(' 01HREMOVE '));
        self::assertSame('01HREMOVE', $repository->attachmentId);
    }

    public function testRemoveRejectsBlankAttachmentId(): void
    {
        $repository = $this->createMock(CatalogAttachmentRepositoryInterface::class);
        $gateway = $this->createMock(AttachmentReferenceGatewayInterface::class);
        $service = new AttachmentService($repository, $gateway);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('attachment_id is required');

        $service->remove('   ');
    }
}
