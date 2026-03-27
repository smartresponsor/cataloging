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

    public function registerBundles(): iterable
    {
        $contents = require dirname(__DIR__).'/config/catalog_bundles.php';
        foreach ($contents as $class => $envs) {
            if (!($envs[$this->getEnvironment()] ?? $envs['all'] ?? false)) {
                continue;
            }

            $bundle = new $class();
            if ($bundle instanceof BundleInterface) {
                yield $bundle;
            }
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/catalog_services*.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../src/Controller/StatusHandler.php', 'attribute');
        $routes->import('../src/Controller/CategoryListHandler.php', 'attribute');
        $routes->import('../src/Controller/CollectionListHandler.php', 'attribute');
        $routes->import('../config/routes/*.yaml');
    }
}
