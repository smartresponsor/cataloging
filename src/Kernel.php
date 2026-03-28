<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const BUNDLES_CONFIG_PATH = '/../config/catalog_bundles.php';
    private const PACKAGES_CONFIG_GLOB = '../config/{packages}/*.yaml';
    private const SERVICES_CONFIG_GLOB = '../config/catalog_services*.yaml';
    private const ROUTES_CONFIG_GLOB = '../config/routes/*.yaml';

    private const ATTRIBUTE_ROUTE_RESOURCES = [
        '../src/Controller/StatusHandler.php',
        '../src/Controller/CategoryListHandler.php',
        '../src/Controller/CollectionListHandler.php',
    ];

    public function registerBundles(): iterable
    {
        $contents = require dirname(__DIR__).self::BUNDLES_CONFIG_PATH;
        foreach ($contents as $class => $envs) {
            if (!($envs[$this->getEnvironment()] ?? $envs['all'] ?? false)) {
                continue;
            }

            yield $this->instantiateBundle($class);
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(self::PACKAGES_CONFIG_GLOB);
        $container->import(self::SERVICES_CONFIG_GLOB);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        foreach (self::ATTRIBUTE_ROUTE_RESOURCES as $resource) {
            $routes->import($resource, 'attribute');
        }

        $routes->import(self::ROUTES_CONFIG_GLOB);
    }

    private function instantiateBundle(string $class): BundleInterface
    {
        $bundle = new $class();
        if (!$bundle instanceof BundleInterface) {
            throw new \LogicException(sprintf('Bundle "%s" must implement %s.', $class, BundleInterface::class));
        }

        return $bundle;
    }
}
