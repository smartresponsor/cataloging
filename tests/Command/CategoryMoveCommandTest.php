<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\Command;

use App\Cataloging\Command\CategoryMoveCommand;
use App\Cataloging\ServiceInterface\CategoryMoveInterface;
use App\Cataloging\ValueObject\CatalogMoveRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryMoveCommandTest extends TestCase
{
    public function testExecutePrintsMovePayloadWithDryRunAndLocale(): void
    {
        $service = new class implements CategoryMoveInterface {
            /** @var array<string,mixed> */
            public array $seen = [];

            public function move(CatalogMoveRequest $request): array
            {
                $this->seen = [
                    'nodeId' => $request->nodeId(),
                    'newParentId' => $request->newParentId(),
                    'treeId' => $request->treeId(),
                    'policy' => $request->policy(),
                    'dryRun' => $request->dryRun(),
                    'locale' => $request->locale(),
                ];

                return [2, [['from' => '/old', 'to' => '/new']]];
            }
        };

        $command = new CategoryMoveCommand($service);
        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'nodeId' => 'cat-100',
            'newParentId' => 'cat-200',
            'treeId' => 'main-tree',
            'policy' => 'strict',
            '--dry-run' => true,
            '--locale' => 'en_US',
        ]);

        self::assertSame(0, $exitCode);
        self::assertSame([
            'nodeId' => 'cat-100',
            'newParentId' => 'cat-200',
            'treeId' => 'main-tree',
            'policy' => 'strict',
            'dryRun' => true,
            'locale' => 'en_US',
        ], $service->seen);

        /** @var array{changed:int,redirects:list<array{from:string,to:string}>} $payload */
        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(2, $payload['changed']);
        self::assertCount(1, $payload['redirects']);
        self::assertSame('/old', $payload['redirects'][0]['from']);
        self::assertSame('/new', $payload['redirects'][0]['to']);
    }
}
