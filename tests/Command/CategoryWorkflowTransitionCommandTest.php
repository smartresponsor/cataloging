<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Cataloging\Tests\Command;

use App\Cataloging\Command\CategoryWorkflowTransitionCommand;
use App\Cataloging\Policy\CatalogCategoryWorkflowEntityPolicy;
use App\Cataloging\Repository\CatalogCategoryWorkflowEntityRepository;
use App\Cataloging\Service\CatalogWorkflowTransitionService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryWorkflowTransitionCommandTest extends TestCase
{
    public function testExecutePrintsTransitionPayload(): void
    {
        $repository = new CatalogCategoryWorkflowEntityRepository();
        $service = new CatalogWorkflowTransitionService($repository, new CatalogCategoryWorkflowEntityPolicy());
        $command = new CategoryWorkflowTransitionCommand($service);

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'categoryId' => 'cat-100',
            'targetState' => 'approved',
            'actorId' => 'ops.user',
            'reason' => 'cli promotion',
        ]);

        self::assertSame(0, $exitCode);

        /** @var array{categoryId:string,fromState:string,toState:string,actorId:string,reason:string} $payload */
        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('cat-100', $payload['categoryId']);
        self::assertSame('draft', $payload['fromState']);
        self::assertSame('approved', $payload['toState']);
        self::assertSame('ops.user', $payload['actorId']);
        self::assertSame('cli promotion', $payload['reason']);
    }
}
