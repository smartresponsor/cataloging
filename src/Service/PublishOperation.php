<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the publish operation application service.
 */
final class PublishOperation
{
    private DraftPolicy $policy;

    /**
     * Initializes the publish operation service collaborators.
     */
    public function __construct(DraftPolicy $policy)
    {
        $this->policy = $policy;
    }

    /**
     * Handles the publish workflow.
     */
    public function publish(Status $status): Status
    {
        if (!$this->policy->allowPublish($status)) {
            throw new \DomainException('Publish is not allowed');
        }

        return new Status(Status::PUBLISHED);
    }
}
