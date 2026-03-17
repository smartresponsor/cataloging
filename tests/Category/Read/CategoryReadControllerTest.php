<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Read;

use App\Controller\CategoryReadController;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CategoryReadControllerTest extends TestCase
{
    public function testChildListReturnsCanonicalEnvelopeAndFiltersUnpublishedRows(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'phones', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Phones'], 'slug' => ['en' => 'phones'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $controller = new CategoryReadController($repository, $this->cache());
        $response = $controller->childList('root', new Request(['locale' => 'en']));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame(1, $payload['count']);
        self::assertSame('root', $payload['node']['id']);
        self::assertSame('phones', $payload['data'][0]['id']);
    }

    public function testAncestorListReturnsBreadcrumbChain(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'phones', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Phones'], 'slug' => ['en' => 'phones'], 'meta' => ['published' => true]],
            ['id' => 'ios', 'taxonomyId' => 'catalog', 'parentId' => 'phones', 'name' => ['en' => 'iOS'], 'slug' => ['en' => 'ios'], 'meta' => ['published' => true]],
        ]);

        $controller = new CategoryReadController($repository, $this->cache());
        $response = $controller->ancestorList('ios', new Request(['locale' => 'en']));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(3, $payload['count']);
        self::assertSame('root', $payload['data'][0]['id']);
        self::assertSame('ios', $payload['data'][2]['id']);
    }

    public function testListReturnsPaginatedPublishedReadSurface(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'phones', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Phones'], 'slug' => ['en' => 'phones'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $controller = new CategoryReadController($repository, $this->cache());
        $response = $controller->list(new Request(['first' => 2, 'locale' => 'en', 'taxonomy' => 'catalog', 'depth' => 3]));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertTrue($payload['ok']);
        self::assertSame(2, $payload['count']);
        self::assertSame('catalog', $payload['taxonomy']);
        self::assertNotEmpty($payload['pageInfo']['after']);
        self::assertSame('root', $payload['data'][0]['id']);
        self::assertSame('phones', $payload['data'][1]['id']);
    }

    public function testChildListReturnsNotFoundForUnknownNode(): void
    {
        $controller = new CategoryReadController(new CategoryRepository(), $this->cache());
        $response = $controller->childList('missing', new Request());
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(404, $response->getStatusCode());
        self::assertFalse($payload['ok']);
        self::assertSame('not_found', $payload['error']);
    }

    private function cache(): CacheInterface
    {
        return new class implements CacheInterface {
            public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null): mixed
            {
                return $callback(new class implements ItemInterface {
                    public function getKey(): string
                    {
                        return 'stub';
                    }

                    public function get(): mixed
                    {
                        return null;
                    }

                    public function isHit(): bool
                    {
                        return false;
                    }

                    public function set(mixed $value): static
                    {
                        return $this;
                    }

                    public function expiresAt(?\DateTimeInterface $expiration): static
                    {
                        return $this;
                    }

                    public function expiresAfter(\DateInterval|int|null $time): static
                    {
                        return $this;
                    }

                    public function tag(string|iterable $tags): static
                    {
                        return $this;
                    }

                    public function getMetadata(): array
                    {
                        return [];
                    }
                }, false);
            }

            public function delete(string $key): bool
            {
                return true;
            }
        };
    }
}
