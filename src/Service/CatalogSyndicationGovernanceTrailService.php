<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategorySyndicationGovernanceTrailRecorded;
use App\Cataloging\EventInterface\CategorySyndicationGovernanceTrailRecordedInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationGovernanceTrailPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationGovernanceTrailServiceInterface;
use App\Cataloging\ValueObject\CategorySyndicationGovernanceTrailRecordRequest;

/**
 * Provides the catalog syndication governance trail service application service.
 */
final readonly class CatalogSyndicationGovernanceTrailService implements CatalogSyndicationGovernanceTrailServiceInterface
{
    /**
     * Initializes the catalog syndication governance trail service service collaborators.
     */
    public function __construct(private CategorySyndicationGovernanceTrailPolicyInterface $policy)
    {
    }

    public function recordTrail(
        CategorySyndicationGovernanceTrailRecordRequest $request,
    ): CategorySyndicationGovernanceTrailRecordedInterface {
        $payloadSet = $request->payloadSet();
        $audit = $request->auditContext();

        $report = $this->policy->buildReport(
            $payloadSet->policyAwarePayload(),
            $payloadSet->deliveryPayload(),
            $payloadSet->historyPayload(),
            $payloadSet->recoveryPayload(),
        );

        return new CategorySyndicationGovernanceTrailRecorded([
            'destinationId' => $report->destinationId(),
            'categoryId' => $report->categoryId(),
            'mediaPolicyMode' => $report->mediaPolicyMode(),
            'strictPublishable' => $report->strictPublishable(),
            'fallbackPublishable' => $report->fallbackPublishable(),
            'resolvedPublishable' => $report->resolvedPublishable(),
            'fallbackUsed' => $report->fallbackUsed(),
            'retryScheduled' => $report->retryScheduled(),
            'historyCounts' => $report->historyCounts(),
            'warnings' => $report->warnings(),
            'checks' => $report->checks(),
            'actorId' => trim($audit->actorId()),
            'reason' => trim($audit->reason()),
        ], new \DateTimeImmutable());
    }
}
