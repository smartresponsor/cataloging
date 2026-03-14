<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Integration;

use App\Service\Integration\Category\WebhookClient;
use App\Service\Security\Category\JwkCache;
use App\Service\Security\Category\OidcJwtVerifier;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class CatalogCategoryWebhookClientTest extends TestCase
{
    public function testDispatchDoesNotThrowForValidInput(): void
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

        $verifier = new OidcJwtVerifier(new JwkCache());
        $webhookClient = new WebhookClient($client, $verifier, 'https://example.test/webhook');
        $webhookClient->dispatch(['event' => 'category.changed', 'id' => 'cat-1']);

        self::assertCount(1, $client->calls);
        self::assertSame('POST', $client->calls[0][0]);
        self::assertSame('https://example.test/webhook', $client->calls[0][1]);
    }
}
