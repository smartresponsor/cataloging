<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

$requiredFiles = [
    'doctrine configuration' => $root . '/config/packages/catalog_doctrine.yaml',
    'category entity' => $root . '/src/Entity/Catalog/CatalogCategoryEntity.php',
    'taxonomy entity' => $root . '/src/Entity/Catalog/CatalogTaxonomyEntity.php',
    'ltree doctrine type' => $root . '/src/Doctrine/Type/LtreeType.php',
];

$missing = [];
foreach ($requiredFiles as $label => $path) {
    if (!is_file($path)) {
        $missing[] = sprintf('%s missing at %s', $label, $path);
    }
}

$doctrineConfig = is_file($requiredFiles['doctrine configuration'])
    ? (string) file_get_contents($requiredFiles['doctrine configuration'])
    : '';

$requiredConfigFragments = [
    'default data connection' => 'default_connection: data',
    'PostgreSQL data driver' => "driver: 'pdo_pgsql'",
    'MySQL infra driver' => "driver: 'pdo_mysql'",
    'ltree type mapping' => 'ltree: App\\Cataloging\\Doctrine\\Type\\LtreeType',
    'catalog entity namespace' => "prefix: 'App\\Cataloging\\Entity\\Catalog'",
];

foreach ($requiredConfigFragments as $label => $fragment) {
    if (!str_contains($doctrineConfig, $fragment)) {
        $missing[] = sprintf('Doctrine config fragment missing: %s', $label);
    }
}

$categoryEntity = is_file($requiredFiles['category entity'])
    ? (string) file_get_contents($requiredFiles['category entity'])
    : '';

$requiredEntityFragments = [
    'category table mapping' => "#[ORM\\Table(name: 'category')]",
    'category path index' => "#[ORM\\Index(name: 'idx_category_path', columns: ['path'])]",
    'tenant workflow index' => "#[ORM\\Index(name: 'idx_category_tenant_workflow', columns: ['tenant', 'workflow_state'])]",
    'ltree path column' => "#[ORM\\Column(type: 'ltree')]",
];

foreach ($requiredEntityFragments as $label => $fragment) {
    if (!str_contains($categoryEntity, $fragment)) {
        $missing[] = sprintf('Category entity mapping fragment missing: %s', $label);
    }
}

if ($missing !== []) {
    fwrite(STDERR, '[check-category-schema] ' . implode(PHP_EOL . '[check-category-schema] ', $missing) . PHP_EOL);
    exit(1);
}

echo '[check-category-schema] Catalog schema prerequisites are present.' . PHP_EOL;
