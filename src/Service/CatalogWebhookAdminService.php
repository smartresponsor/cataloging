<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogWebhookAdminServiceInterface;
use Random\RandomException;

/**
 * Provides the webhook admin service application service.
 */
final class CatalogWebhookAdminService implements CatalogWebhookAdminServiceInterface
{
    /** @var array<string, array{kid:string, secret:non-empty-string}> */
    private static array $keys = [];
    /** @var array<int, array{id:int, target:string, payload:array<string,mixed>, state:string}> */
    private static array $deliveries = [];
    private static int $deliverySequence = 1;

    /**
     * @return array{kid:string,secret:non-empty-string}
     *
     * @throws RandomException
     */
    public function registerKey(string $nameEntity): array
    {
        $token = bin2hex(random_bytes(16));
        self::$keys[$nameEntity] = ['kid' => $nameEntity, 'secret' => $token];

        return self::$keys[$nameEntity];
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

    /**
     * Handles the requeue workflow.
     */
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
