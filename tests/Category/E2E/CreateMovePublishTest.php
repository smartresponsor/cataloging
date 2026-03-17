<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\E2E;

use App\Event\Outbox\OutboxMessage;
use App\Infrastructure\MessengerOutboxDispatcher;
use App\Observability\CatalogProjectionMetrics;
use App\Projection\CategoryProjectionRunner;
use App\Repository\CategoryRepository;
use App\Service\CategoryReleaseWorkflow;
use App\Service\DraftPolicy;
use App\Service\PublishOperation;
use App\Service\TreeOperation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateMovePublishTest extends TestCase
{
    public function testFlow(): void
    {
        $captured = [];

        $bus = new class($captured) implements MessageBusInterface {
            public function __construct(private array &$captured)
            {
            }

            public function dispatch(object $message, array $stamps = []): Envelope
            {
                $this->captured[] = $message;

                return new Envelope($message, $stamps);
            }
        };

        $repository = new CategoryRepository();
        $root = $repository->create('tax-1', null, ['en' => 'Root'], ['en' => 'root'], ['published' => true]);
        $metrics = new CatalogProjectionMetrics();
        $metrics->setLag(12);

        $workflow = new CategoryReleaseWorkflow(
            $repository,
            new TreeOperation(),
            new PublishOperation(new DraftPolicy()),
            new MessengerOutboxDispatcher($bus),
            new CategoryProjectionRunner($metrics),
        );

        $result = $workflow->createMovePublish(
            'actor-1',
            'tax-1',
            null,
            ['en' => 'Laptops'],
            ['en' => 'laptops'],
            ['source' => 'w4'],
            (string) $root['id'],
            3,
        );

        $stored = $repository->bySlug('tax-1', 'laptops', 'en');
        $breadcrumb = $repository->breadcrumb((string) $stored['id'], 'en');

        self::assertSame('published', $result['status']);
        self::assertSame((string) $root['id'], $result['category']['parentId']);
        self::assertSame(3, $result['category']['order']);
        self::assertTrue($result['category']['meta']['published']);

        self::assertSame('/root/laptops', $stored['path']);
        self::assertCount(2, $breadcrumb);
        self::assertSame('Root', $breadcrumb[0]['name']);
        self::assertSame('Laptops', $breadcrumb[1]['name']);

        self::assertCount(2, $captured);
        self::assertContainsOnlyInstancesOf(OutboxMessage::class, $captured);
        self::assertSame('category.moved', $captured[0]->type);
        self::assertSame('category.published', $captured[1]->type);
        self::assertSame(0, $metrics->getLag());
    }
}
