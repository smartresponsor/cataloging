<?php

declare(strict_types=1);

namespace App\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class CatalogServiceConfigurationCoverageTest extends TestCase
{
    public function testServiceConfigurationCoversControllersAndInterfaces(): void
    {
        $config = Yaml::parseFile(dirname(__DIR__, 2) . '/config/catalog_services.yaml');

        self::assertIsArray($config);
        $services = $config['services'] ?? [];

        $this->assertServiceExists($services, 'App\\Controller\\StatusController');
        $this->assertServiceExists($services, 'App\\Controller\\CategoryReadController');
        $this->assertServiceExists($services, 'App\\Controller\\CategoryVirtualController');

        self::assertSame(
            '@App\\Service\\CatalogReadService',
            $services['App\\ServiceInterface\\CatalogReadServiceInterface'] ?? null,
            'CatalogReadServiceInterface must be wired to CatalogReadService.',
        );

        self::assertSame(
            '@App\\Repository\\VirtualCategoryRepository',
            $services['App\\RepositoryInterface\\VirtualCategoryRepositoryInterface'] ?? null,
            'VirtualCategoryRepositoryInterface must be wired to VirtualCategoryRepository.',
        );
    }

    /** @param array<string, mixed> $services */
    private function assertServiceExists(array $services, string $id): void
    {
        self::assertArrayHasKey($id, $services, sprintf('Service "%s" is not registered.', $id));
    }
}
