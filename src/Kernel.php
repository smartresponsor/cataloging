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

    private const BUNDLES_CONFIG_PATH = __DIR__.'/../config/catalog_bundles.php';

    public function registerBundles(): iterable
    {
        $contents = require self::BUNDLES_CONFIG_PATH;
        foreach ($contents as $class => $envs) {
            if (!($envs[$this->getEnvironment()] ?? $envs['all'] ?? false)) {
                continue;
            }

            yield $this->instantiateBundle($class);
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml', null, true);
        $container->import('../config/services.yaml');
        $container->import('../config/services_'.$this->environment.'.yaml', null, true);
        $container->import('../config/catalog_services*.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../config/{routes}.yaml');
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml', null, true);
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
