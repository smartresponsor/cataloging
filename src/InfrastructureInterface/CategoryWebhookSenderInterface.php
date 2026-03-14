<?php

declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\InfrastructureInterface;

interface CategoryWebhookSenderInterface
{
    public function send(array $payload): void;
}
