<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Tests\Tools;

use PHPUnit\Framework\TestCase;

final class CategoryLinterScriptsTest extends TestCase
{
    public function testMirrorCheckSkipsMissingLayerDirectories(): void
    {
        $projectRoot = $this->createProjectRoot();

        self::assertSame(0, $this->runScript('tools/linter/category_mirror_check.php', $projectRoot));
    }

    public function testPrefixCheckDetectsCategoryPrefixViolation(): void
    {
        $projectRoot = $this->createProjectRoot();
        $badFile = $projectRoot . '/src/Service/Category/InvalidName.php';
        mkdir(dirname($badFile), 0777, true);
        file_put_contents($badFile, "<?php\n");

        self::assertSame(1, $this->runScript('tools/linter/category_prefix_check.php', $projectRoot));
    }


    public function testCanonicalRootsCheckPassesForCanonicalStructure(): void
    {
        $projectRoot = $this->createProjectRoot();
        mkdir($projectRoot . '/src/Entity/Catalog', 0777, true);

        self::assertSame(0, $this->runScript('tools/linter/category_canonical_roots_check.php', $projectRoot));
    }

    public function testCanonicalRootsCheckDetectsForbiddenRoot(): void
    {
        $projectRoot = $this->createProjectRoot();
        mkdir($projectRoot . '/src/Catalog', 0777, true);

        self::assertSame(1, $this->runScript('tools/linter/category_canonical_roots_check.php', $projectRoot));
    }

    public function testCanonicalRootsCheckDetectsForbiddenNestedTestsPath(): void
    {
        $projectRoot = $this->createProjectRoot();
        mkdir($projectRoot . '/tests/Feature/Catalog', 0777, true);

        self::assertSame(1, $this->runScript('tools/linter/category_canonical_roots_check.php', $projectRoot));
    }

    private function runScript(string $scriptPath, string $projectRoot): int
    {
        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($projectRoot);
        exec($command, $output, $exitCode);

        return $exitCode;
    }

    private function createProjectRoot(): string
    {
        $path = sys_get_temp_dir() . '/cataloging-lint-' . bin2hex(random_bytes(8));
        mkdir($path, 0777, true);

        return $path;
    }
}
