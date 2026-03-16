<?php
declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 */

use function FastRoute\simpleDispatcher;

require __DIR__ . '/../vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/status', ['SmartResponsor\Http\StatusHandler','handle']);
    $r->addRoute('GET', '/category', ['SmartResponsor\Http\testsListHandler','handle']);
    $r->addRoute('GET', '/collection', ['SmartResponsor\Http\CollectionListHandler','handle']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
if (false !== $pos = strpos($uri, '?')) $uri = substr($uri, 0, $pos);
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404); echo json_encode(['error'=>'not_found']); break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405); echo json_encode(['error'=>'method_not_allowed']); break;
    case FastRoute\Dispatcher::FOUND:
        [$class,$method] = $routeInfo[1];
        (new $class())->$method(); break;
}
