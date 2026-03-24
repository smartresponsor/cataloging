<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

use function FastRoute\simpleDispatcher;

require __DIR__ . '/../vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $routes): void {
    $routes->addRoute('GET', '/status', [App\Controller\StatusHandler::class, '__invoke']);
    $routes->addRoute('GET', '/category', [App\Controller\CategoryListHandler::class, '__invoke']);
    $routes->addRoute('GET', '/collection', [App\Controller\CollectionListHandler::class, '__invoke']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'not_found']);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'method_not_allowed']);
        break;

    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        (new $class())->{$method}();
        break;
}
