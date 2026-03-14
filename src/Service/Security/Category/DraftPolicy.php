<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Service\Security\Category;

use App\Service\Query\Category\Status;

final class DraftPolicy
{
    public function allowPublish(Status $status): bool
    {
        return $status->isDraft();
    }

    public function allowEdit(Status $status): bool
    {
        return $status->isDraft();
    }
}
