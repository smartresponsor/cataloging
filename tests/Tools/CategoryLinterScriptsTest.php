<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\Tools;

use PHPUnit\Framework\TestCase;

final class CategoryLinterScriptsTest extends TestCase
{
    public function testAppNamespaceCheckPassesForCanonicalAppRoot(): void
    {
        $projectRoot = $this->createProjectRoot();
        mkdir($projectRoot.'/src', 0777, true);
        mkdir($projectRoot.'/tests', 0777, true);
        file_put_contents($projectRoot.'/composer.json', json_encode([
            'autoload' => ['psr-4' => ['App\Cataloging\\' => 'src/']],
            'autoload-dev' => ['psr-4' => ['App\Cataloging\\Tests\\' => 'tests/']],
        ], JSON_THROW_ON_ERROR));

        self::assertSame(0, $this->runScript('tools/linter/app_namespace_check.php', $projectRoot));
    }

    public function testAppNamespaceCheckDetectsForbiddenNamespaceRoot(): void
    {
        $projectRoot = $this->createProjectRoot();
        mkdir($projectRoot.'/src', 0777, true);
        mkdir($projectRoot.'/tests', 0777, true);
        file_put_contents($projectRoot.'/composer.json', json_encode([
            'autoload' => ['psr-4' => ['App\Cataloging\\' => 'src/', 'Smartresponsor\\' => 'src/']],
            'autoload-dev' => ['psr-4' => ['App\Cataloging\\Tests\\' => 'tests/']],
        ], JSON_THROW_ON_ERROR));

        self::assertSame(1, $this->runScript('tools/linter/app_namespace_check.php', $projectRoot));
    }

    public function testRepositoryHasCanonicalAppNamespaceRoot(): void
    {
        $projectRoot = dirname(__DIR__, 2);

        self::assertSame(0, $this->runScript('tools/linter/app_namespace_check.php', $projectRoot));
    }

    private function runScript(string $scriptPath, string $projectRoot): int
    {
        $command = escapeshellarg(PHP_BINARY).' '.escapeshellarg($scriptPath).' '.escapeshellarg($projectRoot);
        exec($command, $output, $exitCode);

        return $exitCode;
    }

    private function createProjectRoot(): string
    {
        $path = sys_get_temp_dir().'/cataloging-lint-'.bin2hex(random_bytes(8));
        mkdir($path, 0777, true);

        return $path;
    }
}
