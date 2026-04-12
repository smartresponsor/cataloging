<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the draft policy application service.
 */
final class DraftPolicy
{
    /**
     * Handles the allow publish workflow.
     */
    public function allowPublish(Status $status): bool
    {
        return $status->isDraft();
    }

    /**
     * Handles the allow edit workflow.
     */
    public function allowEdit(Status $status): bool
    {
        return $status->isDraft();
    }
}
