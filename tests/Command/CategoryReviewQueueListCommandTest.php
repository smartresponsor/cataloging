<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Cataloging\Tests\Command;

use App\Cataloging\Command\CategoryReviewQueueListCommand;
use App\Cataloging\Policy\CatalogCategoryReviewAssignmentEntityPolicy;
use App\Cataloging\Policy\CategoryChangeRequestPolicy;
use App\Cataloging\Repository\Catalog\CatalogCategoryChangeRequestRepository;
use App\Cataloging\Repository\Catalog\CatalogCategoryReviewAssignmentRepository;
use App\Cataloging\Service\CatalogChangeRequestService;
use App\Cataloging\Service\CatalogReviewAssignmentService;
use App\Cataloging\Service\CatalogReviewQueueService;
use App\Cataloging\ValueObject\CatalogCategoryReviewAssignmentEntityRequest;
use App\Cataloging\ValueObject\CategoryChangeRequestSubmitRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryReviewQueueListCommandTest extends TestCase
{
    public function testExecutePrintsReviewerQueueAsNdjsonInPriorityOrder(): void
    {
        $changeRequestRepository = new CatalogCategoryChangeRequestRepository();
        $assignmentRepository = new CatalogCategoryReviewAssignmentRepository();

        $changeRequestService = new CatalogChangeRequestService($changeRequestRepository, new CategoryChangeRequestPolicy());
        $assignmentService = new CatalogReviewAssignmentService(
            $changeRequestRepository,
            $assignmentRepository,
            new CatalogCategoryReviewAssignmentEntityPolicy(),
        );
        $queueService = new CatalogReviewQueueService($changeRequestRepository, $assignmentRepository);

        $changeRequestService->submit(new CategoryChangeRequestSubmitRequest('req-urgent', 'cat-urgent', 'submitter.1', 'Urgent category change', ['slug' => 'urgent']));
        $changeRequestService->submit(new CategoryChangeRequestSubmitRequest('req-normal', 'cat-normal', 'submitter.2', 'Normal category change', ['slug' => 'normal']));

        $assignmentService->assign(new CatalogCategoryReviewAssignmentEntityRequest('req-normal', 'reviewer.alpha', 'lead.user', 'normal'));
        $assignmentService->assign(new CatalogCategoryReviewAssignmentEntityRequest('req-urgent', 'reviewer.alpha', 'lead.user', 'urgent'));

        $command = new CategoryReviewQueueListCommand($queueService);
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([
            'reviewer' => 'reviewer.alpha',
        ]);

        self::assertSame(0, $exitCode);

        $lines = array_values(array_filter(array_map('trim', explode(PHP_EOL, trim($tester->getDisplay())))));
        self::assertCount(2, $lines);

        $first = $this->decodeQueueEntry($lines[0]);
        $second = $this->decodeQueueEntry($lines[1]);

        self::assertSame('req-urgent', $first['requestId']);
        self::assertSame('urgent', $first['priority']);
        self::assertSame('req-normal', $second['requestId']);
        self::assertSame('normal', $second['priority']);
        self::assertFalse($first['readyForReview']);
        self::assertContains('request_not_started', $first['readinessWarnings']);
    }

    /**
     * @return array{requestId: string, priority: string, readyForReview: bool, readinessWarnings: list<string>}
     */
    private function decodeQueueEntry(string $json): array
    {
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        return [
            'requestId' => $this->scalarString($decoded['requestId'] ?? ''),
            'priority' => $this->scalarString($decoded['priority'] ?? ''),
            'readyForReview' => (bool) ($decoded['readyForReview'] ?? false),
            'readinessWarnings' => array_values(array_map(
                fn (mixed $warning): string => $this->scalarString($warning),
                is_array($decoded['readinessWarnings'] ?? null) ? $decoded['readinessWarnings'] : [],
            )),
        ];
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
