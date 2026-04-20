<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\Command;

use App\Cataloging\Command\CategoryCompletenessEvaluateCommand;
use App\Cataloging\Command\CategoryMediaReadinessEvaluateCommand;
use App\Cataloging\Command\CategoryMoveCommand;
use App\Cataloging\Command\CategoryPublicationQualityEvaluateCommand;
use App\Cataloging\Command\CategoryReviewAssignCommand;
use App\Cataloging\Command\CategoryReviewQueueListCommand;
use App\Cataloging\Command\CategorySyndicationCategoryGovernanceSummaryCommand;
use App\Cataloging\Command\CategorySyndicationDeliveryFailedListCommand;
use App\Cataloging\Command\CategorySyndicationDestinationGovernanceSummaryCommand;
use App\Cataloging\Command\CategorySyndicationDestinationHistoryCommand;
use App\Cataloging\Command\CategorySyndicationPackagePreviewCommand;
use App\Cataloging\Command\CategorySyndicationRetryScheduleCommand;
use App\Cataloging\Command\CategoryWorkflowTransitionCommand;
use App\Cataloging\RepositoryInterface\CategorySyndicationDeliveryRecordRepositoryInterface;
use App\Cataloging\Service\ArrayValueNormalizer;
use App\Cataloging\ServiceInterface\CatalogCompletenessServiceInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use App\Cataloging\ServiceInterface\CatalogPublicationQualityServiceInterface;
use App\Cataloging\ServiceInterface\CatalogReviewAssignmentServiceInterface;
use App\Cataloging\ServiceInterface\CatalogReviewQueueServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationDestinationGovernanceSummaryServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationGovernanceSummaryServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationHistoryServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationPackageGateServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationRetryServiceInterface;
use App\Cataloging\ServiceInterface\CatalogWorkflowTransitionServiceInterface;
use App\Cataloging\ServiceInterface\CategoryMoveInterface;
use PHPUnit\Framework\TestCase;

final class CategoryCliDiscoverabilityTest extends TestCase
{
    public function testK14CommandsExposeDescriptionAndHelp(): void
    {
        $commands = [
            new CategoryWorkflowTransitionCommand($this->createMock(CatalogWorkflowTransitionServiceInterface::class)),
            new CategoryMoveCommand($this->createMock(CategoryMoveInterface::class)),
            new CategoryReviewQueueListCommand($this->createMock(CatalogReviewQueueServiceInterface::class)),
            new CategoryReviewAssignCommand($this->createMock(CatalogReviewAssignmentServiceInterface::class)),
            new CategoryCompletenessEvaluateCommand($this->createMock(CatalogCompletenessServiceInterface::class)),
            new CategoryPublicationQualityEvaluateCommand($this->createMock(CatalogPublicationQualityServiceInterface::class)),
            new CategoryMediaReadinessEvaluateCommand($this->createMock(CatalogDestinationMediaReadinessServiceInterface::class)),
            new CategorySyndicationPackagePreviewCommand($this->createMock(CatalogSyndicationPackageGateServiceInterface::class)),
            new CategorySyndicationDeliveryFailedListCommand($this->createMock(CategorySyndicationDeliveryRecordRepositoryInterface::class)),
            new CategorySyndicationRetryScheduleCommand(
                $this->createMock(CategorySyndicationDeliveryRecordRepositoryInterface::class),
                $this->createMock(CatalogSyndicationRetryServiceInterface::class),
            ),
            new CategorySyndicationDestinationHistoryCommand($this->createMock(CatalogSyndicationHistoryServiceInterface::class)),
            new CategorySyndicationDestinationGovernanceSummaryCommand($this->createMock(CatalogSyndicationDestinationGovernanceSummaryServiceInterface::class)),
            new CategorySyndicationCategoryGovernanceSummaryCommand($this->createMock(CatalogSyndicationGovernanceSummaryServiceInterface::class), new ArrayValueNormalizer()),
        ];

        foreach ($commands as $command) {
            self::assertNotSame('', trim((string) $command->getDescription()));
            self::assertStringContainsStringIgnoringCase('Use this command', (string) $command->getHelp());
        }
    }
}
