<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Observability;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MetricsController extends AbstractController
{
    #[Route('/metrics', name: 'metrics', methods: ['GET'])]
    public function metrics(): Response
    {
        $path = sys_get_temp_dir().'/sr_metrics/category_http.jsonl';
        $data = '';
        if (file_exists($path)) {
            $loaded = file_get_contents($path);
            if (false !== $loaded) {
                $data = $loaded;
            }
        }

        return new Response($data, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
