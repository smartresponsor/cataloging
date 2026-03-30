<?php
declare(strict_types=1);

use App\Kernel;
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

    echo sprintf(
        "[category-route-discovery-smoke] Verified %d required routes.\n",
        count($requiredRoutes)
    );
    exit(0);
} finally {
    $kernel->shutdown();
}
