<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the sse event application service.
 */
final class SseEvent
{
    private string $event;
    private string $data;

    /**
     * Initializes the sse event service collaborators.
     */
    public function __construct(string $event, string $data)
    {
        $this->event = $event;
        $this->data = $data;
    }

    /**
     * Handles the event workflow.
     */
    public function event(): string
    {
        return $this->event;
    }

    /**
     * Handles the data workflow.
     */
    public function data(): string
    {
        return $this->data;
    }

    /**
     * Handles the to stream workflow.
     */
    public function toStream(): string
    {
        return 'event: '.$this->event."\n".'data: '.$this->data."\n\n";
    }
}
