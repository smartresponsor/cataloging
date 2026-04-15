<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php check_coverage_threshold.php <clover.xml> <min-percent>\n");
    exit(2);
}

$reportPath = $argv[1];
$thresholdInput = trim((string) $argv[2]);
if (!is_numeric($thresholdInput)) {
    fwrite(STDERR, sprintf("Coverage threshold must be numeric, got: %s\n", $argv[2]));
    exit(2);
}

$threshold = (float) $thresholdInput;
if (!is_finite($threshold) || $threshold < 0.0 || $threshold > 100.0) {
    fwrite(STDERR, sprintf("Coverage threshold must be between 0 and 100, got: %s\n", $argv[2]));
    exit(2);
}

if (!is_file($reportPath)) {
    fwrite(STDERR, sprintf("Coverage report not found: %s\n", $reportPath));
    exit(2);
}

$xml = simplexml_load_file($reportPath);
if (false === $xml) {
    fwrite(STDERR, sprintf("Failed to parse coverage report: %s\n", $reportPath));
    exit(2);
}

$metrics = $xml->project->metrics ?? $xml->metrics;
if (!$metrics) {
    fwrite(STDERR, "Coverage metrics missing in Clover report.\n");
    exit(2);
}

$statements = (int) ($metrics['statements'] ?? 0);
$coveredStatements = (int) ($metrics['coveredstatements'] ?? 0);

if (0 === $statements) {
    fwrite(STDERR, "No statements found in coverage report.\n");
    exit(2);
}

$percent = ($coveredStatements / $statements) * 100;
printf("Statement coverage: %.2f%% (threshold: %.2f%%)\n", $percent, $threshold);

if ($percent < $threshold) {
    fwrite(STDERR, sprintf("Coverage gate failed: %.2f%% < %.2f%%\n", $percent, $threshold));
    exit(1);
}

exit(0);
