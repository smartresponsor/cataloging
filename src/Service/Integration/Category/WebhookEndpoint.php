<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Owner: Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
*/

namespace App\Service\Integration\Category;

final class WebhookEndpoint
{
    private string $id;
    private string $url;
    private string $secret;
    private bool $active;

    public function __construct(string $id, string $url, string $secret, bool $active)
    {
        $this->id = $id;
        $this->url = $url;
        $this->secret = $secret;
        $this->active = $active;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function secret(): string
    {
        return $this->secret;
    }

    public function active(): bool
    {
        return $this->active;
    }
}
