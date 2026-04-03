<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-migration-readiness-report.json';

/**
 * @return list<string>
 */
function migrationFiles(string $root): array
{
    $files = glob($root . '/migrations/*.php');
    if ($files === false) {
        return [];
    }

    sort($files, SORT_STRING);

    return array_values(array_map('strval', $files));
}

/**
 * @return list<string>
 */
function extractSqlStatements(string $content): array
{
    $statements = [];
    if (preg_match_all('/addSql\((?:\s*)[\"\']((?:\\.|(?!\1).)*)[\"\']/sU', $content, $matches) === false) {
        return [];
    }

    foreach ($matches[1] as $statement) {
        $statements[] = stripcslashes(trim((string) $statement));
    }

    return $statements;
}

/**
 * @return array{creates:list<string>,drops:list<string>,addColumns:list<string>,dropColumns:list<string>}
 */
function classifySqlStatements(array $statements): array
{
    $creates = [];
    $drops = [];
    $addColumns = [];
    $dropColumns = [];

    foreach ($statements as $statement) {
        if (preg_match('/CREATE\s+TABLE\s+([a-zA-Z0-9_]+)/i', $statement, $matches) === 1) {
            $creates[] = strtolower($matches[1]);
        }
        if (preg_match('/DROP\s+TABLE\s+([a-zA-Z0-9_]+)/i', $statement, $matches) === 1) {
            $drops[] = strtolower($matches[1]);
        }
        if (preg_match('/ALTER\s+TABLE\s+([a-zA-Z0-9_]+)\s+ADD\s+COLUMN\s+([a-zA-Z0-9_]+)/i', $statement, $matches) === 1) {
            $addColumns[] = strtolower($matches[1] . '.' . $matches[2]);
        }
        if (preg_match('/ALTER\s+TABLE\s+([a-zA-Z0-9_]+)\s+DROP\s+COLUMN\s+([a-zA-Z0-9_]+)/i', $statement, $matches) === 1) {
            $dropColumns[] = strtolower($matches[1] . '.' . $matches[2]);
        }
    }

    return [
        'creates' => array_values(array_unique($creates)),
        'drops' => array_values(array_unique($drops)),
        'addColumns' => array_values(array_unique($addColumns)),
        'dropColumns' => array_values(array_unique($dropColumns)),
    ];
}

/**
 * @return array{up:list<string>,down:list<string>}
 */
function extractUpDownStatements(string $content): array
{
    $up = [];
    $down = [];

    if (preg_match('/function\s+up\s*\([^)]*\)\s*:\s*void\s*\{(.*?)\n\s*\}/s', $content, $matches) === 1) {
        $up = extractSqlStatements($matches[1]);
    }

    if (preg_match('/function\s+down\s*\([^)]*\)\s*:\s*void\s*\{(.*?)\n\s*\}/s', $content, $matches) === 1) {
        $down = extractSqlStatements($matches[1]);
    }

    return ['up' => $up, 'down' => $down];
}

$files = migrationFiles($root);
$items = [];
$createMap = [];
$dropMap = [];
$nonCanonicalVersions = [];
$destructiveUpStatements = [];
$destructiveDownStatements = [];

foreach ($files as $file) {
    $relative = str_replace($root . '/', '', $file);
    $content = (string) file_get_contents($file);
    $versionToken = pathinfo($file, PATHINFO_FILENAME);
    $upDown = extractUpDownStatements($content);
    $upClassified = classifySqlStatements($upDown['up']);
    $downClassified = classifySqlStatements($upDown['down']);

    if (preg_match('/^Version\d{14}(?:_[a-z0-9_]+)?$/', $versionToken) !== 1) {
        $nonCanonicalVersions[] = $relative;
    }

    foreach ($upClassified['creates'] as $table) {
        $createMap[$table][] = $relative;
    }
    foreach ($downClassified['drops'] as $table) {
        $dropMap[$table][] = $relative;
    }

    foreach ($upDown['up'] as $statement) {
        if (preg_match('/\bDROP\s+TABLE\b|\bDROP\s+COLUMN\b/i', $statement) === 1) {
            $destructiveUpStatements[] = ['file' => $relative, 'statement' => $statement];
        }
    }
    foreach ($upDown['down'] as $statement) {
        if (preg_match('/\bDROP\s+TABLE\b|\bDROP\s+COLUMN\b/i', $statement) === 1) {
            $destructiveDownStatements[] = ['file' => $relative, 'statement' => $statement];
        }
    }

    $items[] = [
        'file' => $relative,
        'versionToken' => $versionToken,
        'canonicalVersionToken' => preg_match('/^Version\d{14}(?:_[a-z0-9_]+)?$/', $versionToken) === 1,
        'up' => [
            'sqlCount' => count($upDown['up']),
            'creates' => $upClassified['creates'],
            'addColumns' => $upClassified['addColumns'],
        ],
        'down' => [
            'sqlCount' => count($upDown['down']),
            'drops' => $downClassified['drops'],
            'dropColumns' => $downClassified['dropColumns'],
        ],
    ];
}

$duplicateCreates = [];
foreach ($createMap as $table => $sources) {
    if (count($sources) > 1) {
        $duplicateCreates[] = ['table' => $table, 'files' => $sources];
    }
}

$duplicateDrops = [];
foreach ($dropMap as $table => $sources) {
    if (count($sources) > 1) {
        $duplicateDrops[] = ['table' => $table, 'files' => $sources];
    }
}

$zeroDowntimeReady = $duplicateCreates === [] && $nonCanonicalVersions === [] && $destructiveUpStatements === [];
$rollbackDestructiveOnly = $destructiveDownStatements !== [] && $duplicateCreates === [] && $duplicateDrops === [] && $nonCanonicalVersions === [] && $destructiveUpStatements === [];

$status = 'pass';
if (!$zeroDowntimeReady || $duplicateDrops !== []) {
    $status = 'warn';
}

$summary = [
    'migrationCount' => count($items),
    'nonCanonicalVersionCount' => count($nonCanonicalVersions),
    'duplicateCreateCount' => count($duplicateCreates),
    'duplicateDropCount' => count($duplicateDrops),
    'destructiveUpStatementCount' => count($destructiveUpStatements),
    'destructiveDownStatementCount' => count($destructiveDownStatements),
    'zeroDowntimeReady' => $zeroDowntimeReady,
    'rollbackDestructiveOnly' => $rollbackDestructiveOnly,
];

$report = [
    'generatedAt' => date(DATE_ATOM),
    'overallStatus' => $status,
    'summary' => $summary,
    'duplicateCreates' => $duplicateCreates,
    'duplicateDrops' => $duplicateDrops,
    'nonCanonicalVersions' => $nonCanonicalVersions,
    'destructiveUpStatements' => $destructiveUpStatements,
    'destructiveDownStatements' => $destructiveDownStatements,
    'items' => $items,
];

file_put_contents($out, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
printf(
    "[CatalogMigrationReadinessReport] status=%s migrations=%d duplicateCreates=%d nonCanonicalVersions=%d written to %s\n",
    $status,
    $summary['migrationCount'],
    $summary['duplicateCreateCount'],
    $summary['nonCanonicalVersionCount'],
    str_replace($root . DIRECTORY_SEPARATOR, '', $out)
);
