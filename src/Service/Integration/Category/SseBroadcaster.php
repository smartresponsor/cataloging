<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Integration\Category;

final class SseBroadcaster
{
    public function format(string $event, array $data): string
    {
        return "event: {$event}\n".'data: '.json_encode($data, JSON_UNESCAPED_SLASHES)."\n\n";
    }
}
