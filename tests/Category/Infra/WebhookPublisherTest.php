<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Infra;

use App\Infrastructure\ProductWebhookPublisher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class WebhookPublisherTest extends TestCase
{
    public function testPublishSendsJsonPayloadToConfiguredEndpoint(): void
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

        $pub = new ProductWebhookPublisher($client, 'https://example.test/webhooks/category');
        $pub->publish(['event' => 'category.changed', 'id' => 'cat-1']);

        self::assertSame('POST', $capturedMethod);
        self::assertSame('https://example.test/webhooks/category', $capturedUrl);
        self::assertSame(['event' => 'category.changed', 'id' => 'cat-1'], $capturedOptions['json']);
    }
}
