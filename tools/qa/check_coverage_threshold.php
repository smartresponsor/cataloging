<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php check_coverage_threshold.php <clover.xml> <threshold>
");
    exit(1);
}

[$script, $file, $threshold] = $argv;
if (!is_file($file)) {
    fwrite(STDERR, sprintf("Coverage file not found: %s
", $file));
    exit(1);
}

$xml = simplexml_load_file($file);
if (false === $xml) {
    fwrite(STDERR, sprintf("Unable to parse coverage file: %s
", $file));
    exit(1);
}

$metrics = $xml->project->metrics ?? null;
if (null === $metrics) {
    fwrite(STDERR, "Coverage metrics node not found.
");
    exit(1);
}

$elements = (int) ($metrics['elements'] ?? 0);
$coveredelements = (int) ($metrics['coveredelements'] ?? 0);
$coverage = 0.0;
if ($elements > 0) {
    $coverage = ($coveredelements / $elements) * 100;
}

$required = (float) $threshold;
printf("Coverage: %.2f%% (required %.2f%%)
", $coverage, $required);

if ($coverage + 0.00001 < $required) {
    exit(1);
}
