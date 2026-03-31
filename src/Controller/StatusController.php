<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class StatusController extends AbstractController
{
    #[Route('/status', name: 'catalog_status_handler', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'service' => 'catalog',
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
