<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CategoryReviewAssignCommand;
use App\Entity\CategoryChangeRequest;
use App\Policy\CategoryReviewAssignmentPolicy;
use App\Repository\CategoryChangeRequestRepository;
use App\Repository\CategoryReviewAssignmentRepository;
use App\Service\CategoryReviewAssignmentService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryReviewAssignCommandTest extends TestCase
{
    public function testExecutePrintsAssignmentPayload(): void
    {
        $requestRepository = new CategoryChangeRequestRepository();
        $requestRepository->save(CategoryChangeRequest::open('req-100', 'cat-100', 'submitter-1', 'Promote category', ['title' => 'Garden']));

        $service = new CategoryReviewAssignmentService(
            $requestRepository,
            new CategoryReviewAssignmentRepository(),
            new CategoryReviewAssignmentPolicy(),
        );

        $command = new CategoryReviewAssignCommand($service);
        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'requestId' => 'req-100',
            'reviewer' => 'reviewer-1',
            'assignedBy' => 'ops.user',
            '--priority' => 'high',
        ]);

        self::assertSame(0, $exitCode);

        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('req-100', $payload['requestId']);
        self::assertSame('cat-100', $payload['categoryId']);
        self::assertSame('reviewer-1', $payload['assignedReviewer']);
        self::assertSame('high', $payload['priority']);
    }
}
