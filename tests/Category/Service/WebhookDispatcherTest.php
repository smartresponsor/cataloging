<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Service;

use App\Service\WebhookDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class WebhookDispatcherTest extends TestCase
{
    public function testDispatchBuildsSignedJsonWebhookRequest(): void
    {
        $capturedMethod = null;
        $capturedUrl = null;
        $capturedOptions = [];

        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$capturedMethod, &$capturedUrl, &$capturedOptions) {
            $capturedMethod = $method;
            $capturedUrl = $url;
            $capturedOptions = $options;

            return new MockResponse('{"ok":true}', ['http_code' => 202]);
        });

        $dispatcher = new WebhookDispatcher($client, 'secret-key');
        $dispatcher->dispatch('category.changed', ['id' => 'cat-1'], 'https://example.test/webhook');

        self::assertSame('POST', $capturedMethod);
        self::assertSame('https://example.test/webhook', $capturedUrl);
        self::assertSame('category.changed', $capturedOptions['headers']['X-Category-Event']);
        self::assertSame('application/json', $capturedOptions['headers']['Content-Type']);

        $expectedBody = json_encode(['event' => 'category.changed', 'payload' => ['id' => 'cat-1']], JSON_THROW_ON_ERROR);
        self::assertSame($expectedBody, $capturedOptions['body']);
        self::assertSame(hash_hmac('sha256', $expectedBody, 'secret-key'), $capturedOptions['headers']['X-Category-Signature']);
    }
}
