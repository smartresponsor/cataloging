<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category\E2E;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\PantherTestCaseTrait;

final class CreateMovePublishTest extends TestCase
{
    use PantherTestCaseTrait;

    public function testStatusEndpointIsReachableThroughBrowser(): void
    {
        $browser = $this->supportedBrowser();
        if (null === $browser) {
            self::markTestSkipped('Chromium/Edge WebDriver is not available in this environment.');
        }

        $webServerPort = $this->freeLocalPort();
        $browserDriverPort = $this->freeLocalPort();

        $client = self::createPantherClient([
            'browser' => $browser,
            'webServerDir' => dirname(__DIR__, 3).'/public',
            'router' => dirname(__DIR__, 3).'/public/index.php',
            'hostname' => '127.0.0.1',
            'port' => $webServerPort,
            'readinessPath' => '/status',
        ], [], [
            'port' => $browserDriverPort,
        ]);

        $client->request('GET', '/status');
        $currentUrl = $client->getCurrentURL();

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('/status', $currentUrl);
    }

    private function freeLocalPort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);
        self::assertNotFalse($socket, sprintf('Failed to reserve local test port: %d %s', $errorCode, $errorMessage));

        $address = stream_socket_get_name($socket, false);
        fclose($socket);

        self::assertIsString($address);
        $parts = explode(':', $address);

        return (int) end($parts);
    }

    private function supportedBrowser(): ?string
    {
        if ($this->hasExecutable('chromedriver')) {
            return 'chrome';
        }

        if ($this->hasExecutable('msedgedriver')) {
            return 'msedge';
        }

        return null;
    }

    private function hasExecutable(string $name): bool
    {
        $command = '\\' === DIRECTORY_SEPARATOR
            ? sprintf('where %s 2>NUL', escapeshellarg($name))
            : sprintf('command -v %s 2>/dev/null', escapeshellarg($name));
        $result = shell_exec($command);

        return is_string($result) && '' !== trim($result);
    }
}
