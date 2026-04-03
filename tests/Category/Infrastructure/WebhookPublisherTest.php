<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category\Infrastructure;

use App\Infrastructure\HttpWebhookSender;
use App\Infrastructure\OrderWebhookPublisher;
use App\Infrastructure\ProductWebhookPublisher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class WebhookPublisherTest extends TestCase
{
    public function testProductPublisherUsesTimeout(): void
    {
        $capturedOptions = null;
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$capturedOptions): ResponseInterface {
            $capturedOptions = $options;

            return new MockResponse('{}', ['http_code' => 200]);
        });

        $publisher = new ProductWebhookPublisher($client, 'http://example');
        $publisher->publish(['event' => 'category.changed']);

        self::assertSame(5.0, $capturedOptions['timeout'] ?? null);
    }

    public function testOrderPublisherUsesTimeout(): void
    {
        $capturedOptions = null;
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$capturedOptions): ResponseInterface {
            $capturedOptions = $options;

            return new MockResponse('{}', ['http_code' => 200]);
        });

        $publisher = new OrderWebhookPublisher($client, 'http://example');
        $publisher->publish(['event' => 'category.changed']);

        self::assertSame(5.0, $capturedOptions['timeout'] ?? null);
    }

    public function testGenericWebhookSenderUsesTimeout(): void
    {
        $capturedOptions = null;
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$capturedOptions): ResponseInterface {
            $capturedOptions = $options;

            return new MockResponse('{}', ['http_code' => 200]);
        });

        $sender = new HttpWebhookSender($client, 'http://example');
        $sender->send(['event' => 'category.changed']);

        self::assertSame(5.0, $capturedOptions['timeout'] ?? null);
    }
}
