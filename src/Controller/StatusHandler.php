<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Controller;

final class StatusHandler
{
    public function handle(): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'service' => 'category',
            'status' => 'ok',
            'version' => 'rc8',
            'uptime' => @file_get_contents('/proc/uptime') ?: null,
        ]);
    }
}
