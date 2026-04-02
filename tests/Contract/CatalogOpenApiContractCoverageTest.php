<?php

declare(strict_types=1);

namespace App\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class CatalogOpenApiContractCoverageTest extends TestCase
{
    public function testCatalogOpenApiDeclaresCurrentRuntimeSurface(): void
    {
        $spec = Yaml::parseFile(dirname(__DIR__, 2) . '/api/catalog-openapi.yaml');

        self::assertIsArray($spec);
        self::assertSame('3.0.3', $spec['openapi'] ?? null);
        self::assertSame('Catalog API', $spec['info']['title'] ?? null);

        $paths = $spec['paths'] ?? [];

        $this->assertPathHasResponses($paths, '/api/category/list', 'get', ['200']);
        $this->assertPathHasResponses($paths, '/api/category/{id}', 'get', ['200', '404']);
        $this->assertPathHasResponses($paths, '/api/category/{id}/descendants', 'get', ['200', '404']);
        $this->assertPathHasResponses($paths, '/api/category/{id}/child', 'get', ['200', '404']);
        $this->assertPathHasResponses($paths, '/api/category/{id}/ancestor', 'get', ['200', '404']);
        $this->assertPathHasResponses($paths, '/api/category/attachment', 'get', ['200']);
        $this->assertPathHasResponses($paths, '/api/category/attachment', 'post', ['201', '400']);
        $this->assertPathHasResponses($paths, '/api/category/attachment/{attachmentId}', 'delete', ['200', '400', '404']);
        $this->assertPathHasResponses($paths, '/api/category/collection', 'post', ['200', '400']);
        $this->assertPathHasResponses($paths, '/api/category/virtual/preview', 'post', ['200', '400']);
        $this->assertPathHasResponses($paths, '/api/category/virtual/apply/{id}', 'post', ['200', '404']);
        $this->assertPathHasResponses($paths, '/api/doc', 'get', ['200']);
        $this->assertPathHasResponses($paths, '/api/doc.json', 'get', ['200']);
    }

    /**
     * @param array<string, mixed> $paths
     * @param list<string> $expectedResponses
     */
    private function assertPathHasResponses(array $paths, string $path, string $method, array $expectedResponses): void
    {
        self::assertArrayHasKey($path, $paths, sprintf('Missing OpenAPI path "%s".', $path));
        self::assertArrayHasKey($method, $paths[$path], sprintf('Missing %s operation for path "%s".', strtoupper($method), $path));

        $operation = $paths[$path][$method];
        self::assertIsArray($operation);
        self::assertArrayHasKey('responses', $operation, sprintf('Missing responses block for %s %s.', strtoupper($method), $path));

        foreach ($expectedResponses as $statusCode) {
            self::assertArrayHasKey(
                $statusCode,
                $operation['responses'],
                sprintf('Missing %s response for %s %s.', $statusCode, strtoupper($method), $path),
            );
        }
    }
}
