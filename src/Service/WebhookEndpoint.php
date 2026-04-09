<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the webhook endpoint application service.
 */
final class WebhookEndpoint
{
    private string $id;
    private string $url;
    private string $secret;
    private bool $active;
    /**
     * Initializes the webhook endpoint service collaborators.
     */
    public function __construct(string $id, string $url, string $secret, bool $active)
    {
        $this->id = $id;
        $this->url = $url;
        $this->secret = $secret;
        $this->active = $active;
    }
    /**
     * Handles the id workflow.
     */
    public function id(): string
    {
        return $this->id;
    }
    /**
     * Handles the url workflow.
     */
    public function url(): string
    {
        return $this->url;
    }
    /**
     * Handles the secret workflow.
     */
    public function secret(): string
    {
        return $this->secret;
    }
    /**
     * Handles the active workflow.
     */
    public function active(): bool
    {
        return $this->active;
    }
}
