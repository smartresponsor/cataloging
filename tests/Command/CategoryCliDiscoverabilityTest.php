<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CategoryCompletenessEvaluateCommand;
use App\Command\CategoryMediaReadinessEvaluateCommand;
use App\Command\CategoryPublicationQualityEvaluateCommand;
use App\Command\CategoryReviewAssignCommand;
use App\Command\CategoryReviewQueueListCommand;
use App\Command\CategorySyndicationCategoryGovernanceSummaryCommand;
use App\Command\CategorySyndicationDeliveryFailedListCommand;
use App\Command\CategorySyndicationDestinationGovernanceSummaryCommand;
use App\Command\CategorySyndicationDestinationHistoryCommand;
use App\Command\CategorySyndicationPackagePreviewCommand;
use App\Command\CategorySyndicationRetryScheduleCommand;
use App\Command\CategoryWorkflowTransitionCommand;
use App\RepositoryInterface\CategorySyndicationDeliveryRecordRepositoryInterface;
use App\ServiceInterface\CategoryCompletenessServiceInterface;
use App\ServiceInterface\CategoryDestinationMediaReadinessServiceInterface;
use App\ServiceInterface\CategoryPublicationQualityServiceInterface;
use App\ServiceInterface\CategoryReviewAssignmentServiceInterface;
use App\ServiceInterface\CategoryReviewQueueServiceInterface;
use App\ServiceInterface\CategorySyndicationCategoryGovernanceSummaryServiceInterface;
use App\ServiceInterface\CategorySyndicationDestinationGovernanceSummaryServiceInterface;
use App\ServiceInterface\CategorySyndicationHistoryServiceInterface;
use App\ServiceInterface\CategorySyndicationPackageGateServiceInterface;
use App\ServiceInterface\CategorySyndicationRetryServiceInterface;
use App\ServiceInterface\CategoryWorkflowTransitionServiceInterface;
use PHPUnit\Framework\TestCase;

final class CategoryCliDiscoverabilityTest extends TestCase
{
    public function testK13CommandsExposeDescriptionAndHelp(): void
    {
        $commands = [
            new CategoryWorkflowTransitionCommand($this->createMock(CategoryWorkflowTransitionServiceInterface::class)),
            new CategoryReviewQueueListCommand($this->createMock(CategoryReviewQueueServiceInterface::class)),
            new CategoryReviewAssignCommand($this->createMock(CategoryReviewAssignmentServiceInterface::class)),
            new CategoryCompletenessEvaluateCommand($this->createMock(CategoryCompletenessServiceInterface::class)),
            new CategoryPublicationQualityEvaluateCommand($this->createMock(CategoryPublicationQualityServiceInterface::class)),
            new CategoryMediaReadinessEvaluateCommand($this->createMock(CategoryDestinationMediaReadinessServiceInterface::class)),
            new CategorySyndicationPackagePreviewCommand($this->createMock(CategorySyndicationPackageGateServiceInterface::class)),
            new CategorySyndicationDeliveryFailedListCommand($this->createMock(CategorySyndicationDeliveryRecordRepositoryInterface::class)),
            new CategorySyndicationRetryScheduleCommand(
                $this->createMock(CategorySyndicationDeliveryRecordRepositoryInterface::class),
                $this->createMock(CategorySyndicationRetryServiceInterface::class),
            ),
            new CategorySyndicationDestinationHistoryCommand($this->createMock(CategorySyndicationHistoryServiceInterface::class)),
            new CategorySyndicationDestinationGovernanceSummaryCommand($this->createMock(CategorySyndicationDestinationGovernanceSummaryServiceInterface::class)),
            new CategorySyndicationCategoryGovernanceSummaryCommand($this->createMock(CategorySyndicationCategoryGovernanceSummaryServiceInterface::class)),
        ];

        foreach ($commands as $command) {
            self::assertNotSame('', trim((string) $command->getDescription()));
            self::assertStringContainsStringIgnoringCase('Use this command', (string) $command->getHelp());
        }
    }
}
