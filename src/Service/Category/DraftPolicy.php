<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Category;

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
