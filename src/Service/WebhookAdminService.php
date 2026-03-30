<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\WebhookAdminServiceInterface;

final class WebhookAdminService implements WebhookAdminServiceInterface
{
    /** @var array<string, array{kid:string, secret:non-empty-string}> */
    private static array $keys = [];
    /** @var array<int, array{id:int, target:string, payload:array<string,mixed>, state:string}> */
    private static array $deliveries = [];
    private static int $deliverySequence = 1;

    /** @return array{kid:string,secret:non-empty-string} */
    public function registerKey(string $name): array
    {
        $token = bin2hex(random_bytes(16));
        self::$keys[$name] = ['kid' => $name, 'secret' => $token];

        return self::$keys[$name];
    }

    /** @param array<string,mixed> $payload */
    public function scheduleDelivery(string $target, array $payload): int
    {
        $id = self::$deliverySequence++;
        self::$deliveries[$id] = [
            'id' => $id,
            'target' => $target,
            'payload' => $payload,
            'state' => 'queued',
        ];

        return $id;
    }

    public function requeue(int $limit): int
    {
        $requeued = 0;
        foreach (self::$deliveries as &$delivery) {
            if ($requeued >= $limit) {
                break;
            }
            if ('dead-letter' === $delivery['state']) {
                $delivery['state'] = 'queued';
                ++$requeued;
            }
        }
        unset($delivery);

        return $requeued;
    }
}
