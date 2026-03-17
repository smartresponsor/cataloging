<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Service;

use App\Service\WebhookDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class WebhookDispatcherTest extends TestCase
{
    public function testDispatchBuildsSignedJsonWebhookRequest(): void
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

        $dispatcher = new WebhookDispatcher($client, 'secret-key');
        $dispatcher->dispatch('category.changed', ['id' => 'cat-1'], 'https://example.test/webhook');

        self::assertSame('POST', $capturedMethod);
        self::assertSame('https://example.test/webhook', $capturedUrl);
        self::assertSame('category.changed', $capturedOptions['headers']['X-Category-Event'] ?? null);
        self::assertSame('application/json', $capturedOptions['headers']['Content-Type'] ?? null);

        $expectedBody = json_encode(['event' => 'category.changed', 'payload' => ['id' => 'cat-1']], JSON_THROW_ON_ERROR);
        self::assertSame($expectedBody, $capturedOptions['body'] ?? null);
        self::assertSame(hash_hmac('sha256', $expectedBody, 'secret-key'), $capturedOptions['headers']['X-Category-Signature'] ?? null);
    }
}
