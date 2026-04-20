<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the webhook endpoint application service.
 */
final class WebhookEndpoint
{
    private string $endpointId;
    private string $endpointUrl;
    private string $secret;
    private bool $active;

    /**
     * Initializes the webhook endpoint service collaborators.
     */
    public function __construct(string $endpointId, string $endpointUrl, string $secret, bool $active)
    {
        $this->endpointId = $endpointId;
        $this->endpointUrl = $endpointUrl;
        $this->secret = $secret;
        $this->active = $active;
    }

    /**
     * Handles the id workflow.
     */
    public function id(): string
    {
        return $this->endpointId;
    }

    /**
     * Handles the url workflow.
     */
    public function url(): string
    {
        return $this->endpointUrl;
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
