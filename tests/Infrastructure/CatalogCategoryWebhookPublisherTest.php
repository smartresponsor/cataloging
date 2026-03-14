<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Infrastructure;

use App\Infrastructure\ProductWebhookPublisher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class CatalogCategoryWebhookPublisherTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $client = new class implements HttpClientInterface {
            public array $calls = [];

            public function request(string $method, string $url, array $options = []): ResponseInterface
            {
                $this->calls[] = [$method, $url, $options];

                return new class implements ResponseInterface {
                    public function getStatusCode(): int
                    {
                        return 200;
                    }

                    public function getHeaders(bool $throw = true): array
                    {
                        return [];
                    }

                    public function getContent(bool $throw = true): string
                    {
                        return '';
                    }

                    public function toArray(bool $throw = true): array
                    {
                        return [];
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

            public function stream(ResponseInterface|\Traversable|array $responses, ?float $timeout = null): ResponseStreamInterface
            {
                return new class implements ResponseStreamInterface {
                    public function current(): ChunkInterface
                    {
                        return new class implements ChunkInterface {
                            public function isTimeout(): bool
                            {
                                return false;
                            }

                            public function isFirst(): bool
                            {
                                return false;
                            }

                            public function isLast(): bool
                            {
                                return true;
                            }

                            public function getInformationalStatus(): ?array
                            {
                                return null;
                            }

                            public function getContent(): string
                            {
                                return '';
                            }

                            public function getOffset(): int
                            {
                                return 0;
                            }

                            public function getError(): ?string
                            {
                                return null;
                            }
                        };
                    }

                    public function key(): ResponseInterface
                    {
                        return new class implements ResponseInterface {
                            public function getStatusCode(): int
                            {
                                return 200;
                            }

                            public function getHeaders(bool $throw = true): array
                            {
                                return [];
                            }

                            public function getContent(bool $throw = true): string
                            {
                                return '';
                            }

                            public function toArray(bool $throw = true): array
                            {
                                return [];
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

                    public function next(): void
                    {
                    }

                    public function rewind(): void
                    {
                    }

                    public function valid(): bool
                    {
                        return false;
                    }
                };
            }

            public function withOptions(array $options): static
            {
                return $this;
            }
        };

        $publisher = new ProductWebhookPublisher($client, 'http://example');
        $publisher->publish(['event' => 'category.changed']);

        self::assertCount(1, $client->calls);
    }
}
