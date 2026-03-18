<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class StatusHandler extends AbstractController
{
    #[Route('/status', name: 'category_status_handler', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'service' => 'category',
            'status' => 'ok',
            'version' => 'rc8',
            'uptime' => $this->readUptime(),
        ]);
    }

    private function readUptime(): ?string
    {
        if (!is_readable('/proc/uptime')) {
            return null;
        }

        $uptime = file('/proc/uptime', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (false === $uptime || [] === $uptime) {
            return null;
        }

        return $uptime[0];
    }
}
