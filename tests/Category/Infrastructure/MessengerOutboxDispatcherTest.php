<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Infrastructure;

use App\Event\Outbox\OutboxMessage;
use App\Infrastructure\MessengerOutboxDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerOutboxDispatcherTest extends TestCase
{
    public function testDispatchPassesEventToMessageBus(): void
    {
        $captured = null;

        $bus = new class($captured) implements MessageBusInterface {
            public mixed $captured = null;

            public function __construct(&$captured)
            {
                $this->captured = &$captured;
            }

            public function dispatch(object $message, array $stamps = []): Envelope
            {
                $this->captured = $message;

                return new Envelope($message, $stamps);
            }
        };

        $dispatcher = new MessengerOutboxDispatcher($bus);
        $dispatcher->dispatch(['type' => 'category.moved', 'id' => 'cat-1', 'payload' => ['parentId' => 'root-1']]);

        self::assertInstanceOf(OutboxMessage::class, $captured);
        self::assertSame('category.moved', $captured->type);
        self::assertSame('cat-1', $captured->id);
        self::assertSame('{"parentId":"root-1"}', $captured->payload);
    }
}
