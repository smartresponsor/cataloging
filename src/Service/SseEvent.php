<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class SseEvent
{
    private string $event;
    private string $data;

    public function __construct(string $event, string $data)
    {
        $this->event = $event;
        $this->data = $data;
    }

    public function event(): string
    {
        return $this->event;
    }

    public function data(): string
    {
        return $this->data;
    }

    public function toStream(): string
    {
        return "event: {$this->event}\ndata: {$this->data}\n\n";
    }
}
