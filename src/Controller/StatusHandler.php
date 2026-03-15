<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 */

namespace SmartResponsor\Http;

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
