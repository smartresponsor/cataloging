<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$targets = [
    'src/Service/Api/GraphqlResolver.php' => 'namespace App\\Service\\Category\\Api;',
    'src/ServiceInterface/Api/GraphqlResolverInterface.php' => 'namespace App\\ServiceInterface\\Category\\Api;',
];

$fail = 0;
foreach ($targets as $path => $needle) {
    if (!is_file($path)) {
        fwrite(STDERR, "MISSING {$path}\n");
        $fail = 1;
        continue;
    }
    $content = file_get_contents($path);
    if ($content === false || !str_contains($content, $needle)) {
        fwrite(STDERR, "DRIFT {$path}\n");
        $fail = 1;
    }
}

exit($fail);
