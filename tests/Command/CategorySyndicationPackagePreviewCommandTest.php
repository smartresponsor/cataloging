<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Tests\Command;

use App\Command\CategorySyndicationPackagePreviewCommand;
use App\ServiceInterface\CatalogSyndicationPackageGateServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategorySyndicationPackagePreviewCommandTest extends TestCase
{
    public function testExecutePrintsPreviewPackage(): void
    {
        $service = $this->createMock(CatalogSyndicationPackageGateServiceInterface::class);
        $service->method('buildGatedPublishPackage')->willReturn(new class implements \App\EventInterface\CategorySyndicationPackageGatedInterface {
            public function __construct(private readonly array $payload = ['publishable' => true, 'packageId' => 'pkg-1'])
            {
            }

            public function packageId(): string
            {
                return 'pkg-1';
            }

            public function destinationId(): string
            {
                return 'dest-1';
            }

            public function categoryId(): string
            {
                return 'cat-1';
            }

            public function version(): string
            {
                return '1';
            }

            public function localeMode(): string
            {
                return 'per_locale';
            }

            public function payloadData(): array
            {
                return [];
            }

            public function fieldMap(): array
            {
                return [];
            }

            public function requiredFields(): array
            {
                return [];
            }

            public function packageMissingRequiredFields(): array
            {
                return [];
            }

            public function mediaRequiredMissing(): array
            {
                return [];
            }

            public function warnings(): array
            {
                return [];
            }

            public function checks(): array
            {
                return [];
            }

            public function matchedBindingIds(): array
            {
                return [];
            }

            public function publishable(): bool
            {
                return true;
            }

            public function actorId(): string
            {
                return 'cli';
            }

            public function reason(): string
            {
                return 'preview';
            }

            public function occurredAt(): \DateTimeImmutable
            {
                return new \DateTimeImmutable();
            }

            public function payload(): array
            {
                return $this->payload;
            }
        });

        $command = new CategorySyndicationPackagePreviewCommand($service);
        $tester = new CommandTester($command);
        $tester->execute([
            'categoryId' => 'cat-1',
            '--mapping' => '{"fieldMap":{"name":"title"},"requiredFields":["title"],"payload":{"title":"Catalog"}}',
            '--destination' => '{"destinationId":"dest-1","channel":"web","locale":"en_US"}',
        ]);

        self::assertStringContainsString('"packageId":"pkg-1"', str_replace(['
', '
', ' '], '', $tester->getDisplay()));
    }
}
