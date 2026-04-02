<?php

declare(strict_types=1);

namespace App\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class CatalogOpenApiRuntimeRouteParityTest extends TestCase
{
    public function testAllApiRoutesAreDeclaredInRuntimeOpenApi(): void
    {
        $spec = Yaml::parseFile(dirname(__DIR__, 2) . '/api/catalog-openapi.runtime.yaml');
        $paths = array_keys($spec['paths'] ?? []);

        $controllerDir = dirname(__DIR__, 2) . '/src/Controller';
        $declaredRoutes = [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($controllerDir));
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname()) ?: '';

            if (preg_match_all('/#\[Route\(\'([^\']+)\'/m', $contents, $matches)) {
                foreach ($matches[1] as $path) {
                    if (str_starts_with($path, '/api/')) {
                        $declaredRoutes[] = $path;
                    }
                }
            }
        }

        $declaredRoutes = array_unique($declaredRoutes);

        foreach ($declaredRoutes as $route) {
            self::assertContains($route, $paths, sprintf('Missing route in OpenAPI: %s', $route));
        }
    }
}
