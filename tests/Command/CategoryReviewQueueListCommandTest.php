<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Tests\Command;

use App\Command\CategoryReviewQueueListCommand;
use App\Policy\CategoryChangeRequestPolicy;
use App\Policy\CategoryReviewAssignmentPolicy;
use App\Repository\CategoryChangeRequestRepository;
use App\Repository\CategoryReviewAssignmentRepository;
use App\Service\CategoryChangeRequestService;
use App\Service\CategoryReviewAssignmentService;
use App\Service\CategoryReviewQueueService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryReviewQueueListCommandTest extends TestCase
{
    public function testExecutePrintsReviewerQueueAsNdjsonInPriorityOrder(): void
    {
        $changeRequestRepository = new CategoryChangeRequestRepository();
        $assignmentRepository = new CategoryReviewAssignmentRepository();

        $changeRequestService = new CategoryChangeRequestService($changeRequestRepository, new CategoryChangeRequestPolicy());
        $assignmentService = new CategoryReviewAssignmentService(
            $changeRequestRepository,
            $assignmentRepository,
            new CategoryReviewAssignmentPolicy(),
        );
        $queueService = new CategoryReviewQueueService($changeRequestRepository, $assignmentRepository);

        $changeRequestService->submit('req-urgent', 'cat-urgent', 'submitter.1', 'Urgent category change', ['slug' => 'urgent']);
        $changeRequestService->submit('req-normal', 'cat-normal', 'submitter.2', 'Normal category change', ['slug' => 'normal']);

        $assignmentService->assign('req-normal', 'reviewer.alpha', 'lead.user', 'normal');
        $assignmentService->assign('req-urgent', 'reviewer.alpha', 'lead.user', 'urgent');

        $command = new CategoryReviewQueueListCommand($queueService);
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([
            'reviewer' => 'reviewer.alpha',
        ]);

        self::assertSame(0, $exitCode);

        $lines = array_values(array_filter(array_map('trim', explode(PHP_EOL, trim($tester->getDisplay())))));
        self::assertCount(2, $lines);

        $first = json_decode($lines[0], true, 512, JSON_THROW_ON_ERROR);
        $second = json_decode($lines[1], true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('req-urgent', $first['requestId']);
        self::assertSame('urgent', $first['priority']);
        self::assertSame('req-normal', $second['requestId']);
        self::assertSame('normal', $second['priority']);
        self::assertFalse($first['readyForReview']);
        self::assertContains('request_not_started', $first['readinessWarnings']);
    }
}
