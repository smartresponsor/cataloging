<?php
declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/
namespace App\Layer\Category;

final class PublishOperation
{
    private DraftPolicy $policy;

    public function __construct(DraftPolicy $policy)
    {
        $this->policy = $policy;
    }

    public function publish(Status $status): Status
    {
        if (!$this->policy->allowPublish($status)) {
            throw new \DomainException('Publish is not allowed');
        }
        return new Status(Status::PUBLISHED);
    }
}
