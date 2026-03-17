<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Infra;

use App\Infrastructure\ProductWebhookPublisher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class WebhookPublisherTest extends TestCase
{
    public function testPublishSendsJsonPayloadToConfiguredEndpoint(): void
    {
        $capturedMethod = null;
        $capturedUrl = null;
        $capturedOptions = [];

        $client = new class($capturedMethod, $capturedUrl, $capturedOptions) implements HttpClientInterface {
            public function __construct(
                private ?string &$capturedMethod,
                private ?string &$capturedUrl,
                private array &$capturedOptions,
            ) {
            }

            public function request(string $method, string $url, array $options = []): ResponseInterface
            {
                $this->capturedMethod = $method;
                $this->capturedUrl = $url;
                $this->capturedOptions = $options;

                return new class implements ResponseInterface {
                    public function getStatusCode(): int
                    {
                        return 202;
                    }

                    public function getHeaders(bool $throw = true): array
                    {
                        return [];
                    }

                    public function getContent(bool $throw = true): string
                    {
                        return '{"ok":true}';
                    }

                    public function toArray(bool $throw = true): array
                    {
                        return ['ok' => true];
                    }

                    public function cancel(): void
                    {
                    }

                    public function getInfo(?string $type = null): mixed
                    {
                        return null;
                    }
                };
            }

            public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
            {
                return new class implements ResponseStreamInterface, \IteratorAggregate {
                    public function getIterator(): \Traversable
                    {
                        return new \ArrayIterator([]);
                    }
                };
            }

            public function withOptions(array $options): static
            {
                return $this;
            }
        };

        $pub = new ProductWebhookPublisher($client, 'https://example.test/webhooks/category');
        $pub->publish(['event' => 'category.changed', 'id' => 'cat-1']);

        self::assertSame('POST', $capturedMethod);
        self::assertSame('https://example.test/webhooks/category', $capturedUrl);
        self::assertSame(['event' => 'category.changed', 'id' => 'cat-1'], $capturedOptions['json'] ?? null);
    }
}
