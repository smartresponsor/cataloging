<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CategoryMediaReadinessEvaluateCommand;
use App\ServiceInterface\CategoryDestinationMediaReadinessServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryMediaReadinessEvaluateCommandTest extends TestCase
{
    public function testExecutePrintsJsonPayload(): void
    {
        $service = $this->createMock(CategoryDestinationMediaReadinessServiceInterface::class);
        $service->method('evaluate')->willReturn(new class implements \App\EventInterface\CategoryDestinationMediaReadinessEvaluatedInterface {
            public function __construct(private readonly array $payload = ['publishable' => true, 'checks' => ['destinationMediaPublishable' => true]])
            {
            }

            public function destinationId(): string
            {
                return 'dest-1';
            }

            public function categoryId(): string
            {
                return 'cat-1';
            }

            public function channel(): string
            {
                return 'web';
            }

            public function locale(): string
            {
                return 'en_US';
            }

            public function publishable(): bool
            {
                return true;
            }

            public function requiredMissing(): array
            {
                return [];
            }

            public function warnings(): array
            {
                return [];
            }

            public function checks(): array
            {
                return ['destinationMediaPublishable' => true];
            }

            public function matchedBindingIds(): array
            {
                return [];
            }

            public function actorId(): string
            {
                return 'ops';
            }

            public function reason(): string
            {
                return 'check';
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

        $command = new CategoryMediaReadinessEvaluateCommand($service);
        $tester = new CommandTester($command);
        $tester->execute([
            'categoryId' => 'cat-1',
            'actorId' => 'ops',
            'reason' => 'check',
            '--destination' => '{"channel":"web","locale":"en_US"}',
        ]);

        self::assertStringContainsString('"publishable":true', str_replace(['', '
', ' '], '', $tester->getDisplay()));
    }
}
