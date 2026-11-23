<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

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
