<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service\Workflow\Category;

use App\Service\Query\Category\Status;
use App\Service\Security\Category\DraftPolicy;

final class PublishOperation
{
    public function __construct(private readonly DraftPolicy $policy)
    {
    }

    public function publish(Status $status): Status
    {
        if (!$this->policy->allowPublish($status)) {
            throw new \DomainException('Publish is not allowed');
        }

        return new Status(Status::PUBLISHED);
    }
}
