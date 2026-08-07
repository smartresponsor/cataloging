<?php
declare(strict_types=1);

use App\Cataloging\Kernel;
use Symfony\Component\Routing\RouteCollection;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$kernel = new Kernel('test', true);
$kernel->boot();

try {
    $router = $kernel->getContainer()->get('router');
    if (!method_exists($router, 'getRouteCollection')) {
        fwrite(STDERR, "[category-route-discovery-smoke] Router does not expose getRouteCollection().\n");
        exit(1);
    }

    /** @var RouteCollection $collection */
    $collection = $router->getRouteCollection();
    $requiredRoutes = [
        'cataloging_catalog_index_explicit',
        'cataloging_catalog_index',
        'cataloging_catalog_index_no_slash',
        'cataloging_catalog_category_show',
        'api_category_storefront',
        'api_category_attachment_list',
        'api_category_attachment_add',
        'api_category_collection',
        'api_category_list',
        'api_category_child_list',
        'api_category_ancestor_list',
        'api_category_virtual_preview',
        'api_category_virtual_apply',
        'app.swagger_ui',
        'app.swagger',
    ];

    $missingRoutes = [];
    foreach ($requiredRoutes as $routeName) {
        if (null === $collection->get($routeName)) {
            $missingRoutes[] = $routeName;
        }
    }

    if ([] !== $missingRoutes) {
        fwrite(STDERR, sprintf(
            "[category-route-discovery-smoke] Missing routes: %s\n",
            implode(', ', $missingRoutes)
        ));
        exit(1);
    }

    $expectedPaths = [
        'cataloging_catalog_index_explicit' => '/catalog/index',
        'cataloging_catalog_index' => '/catalog/',
        'cataloging_catalog_index_no_slash' => '/catalog',
        'cataloging_catalog_category_show' => '/catalog/category/{slug}',
        'api_category_storefront' => '/api/catalog/category/storefront',
    ];

    $invalidPaths = [];
    foreach ($expectedPaths as $routeName => $expectedPath) {
        $route = $collection->get($routeName);
        if (null === $route || $route->getPath() !== $expectedPath) {
            $invalidPaths[] = sprintf(
                '%s=%s expected=%s',
                $routeName,
                null === $route ? 'missing' : $route->getPath(),
                $expectedPath,
            );
        }
    }

    if ([] !== $invalidPaths) {
        fwrite(STDERR, sprintf(
            "[category-route-discovery-smoke] Invalid storefront route paths: %s\n",
            implode('; ', $invalidPaths),
        ));
        exit(1);
    }

    echo sprintf(
        "[category-route-discovery-smoke] Verified %d required routes and %d public storefront paths.\n",
        count($requiredRoutes),
        count($expectedPaths),
    );
    exit(0);
} finally {
    $kernel->shutdown();
}
