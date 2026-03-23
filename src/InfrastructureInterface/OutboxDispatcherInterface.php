<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\InfrastructureInterface;

interface OutboxDispatcherInterface
{
    public function dispatch(array $event): void;
}
