<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$targets = [
    'src/Service/Command/Category/CategoryCommandService.php',
    'src/ServiceInterface/Command/Category/CategoryCommandServiceInterface.php',
    'src/Service/Query/Category/CategoryEntityRepositoryInterface.php',
    'src/ServiceInterface/Query/Category/CategoryListRepositoryInterface.php',
    'src/Runner/CategoryProjectionLoopRunner.php',
    'src/RunnerInterface/CategoryProjectionLoopRunnerInterface.php',
    'src/Service/Workflow/Category/CategoryWorkflowCacheInvalidator.php',
    'src/Api/Graphql/CategoryNodeResolver.php',
];

$rows = [];
foreach ($targets as $path) {
    $rows[] = ['path' => $path, 'exists' => file_exists($root . '/' . $path)];
}

echo json_encode(['rows' => $rows], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
