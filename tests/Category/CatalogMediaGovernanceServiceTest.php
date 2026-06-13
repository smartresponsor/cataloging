<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CategoryMediaGovernancePolicy;
use App\Cataloging\Repository\Catalog\CatalogCategoryMediaBindingRepository;
use App\Cataloging\Service\CatalogMediaGovernanceService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CatalogCategoryMediaBindingEntityScope;
use App\Cataloging\ValueObject\CatalogCategoryMediaBindingEntityState;
use App\Cataloging\ValueObject\CategoryMediaBindRequest;
use PHPUnit\Framework\TestCase;

final class CatalogMediaGovernanceServiceTest extends TestCase
{
    public function testBindReturnsMediaBindingEventWithNormalizedPayload(): void
    {
        $repository = new CatalogCategoryMediaBindingRepository();
        $service = new CatalogMediaGovernanceService($repository, new CategoryMediaGovernancePolicy());

        $payload = $this->normalizePayload($service->bind(
            new CategoryMediaBindRequest(
                new CatalogCategoryMediaBindingEntityScope(
                    'binding-201',
                    'category-701',
                    'asset-501',
                    'banner',
                    ['storefront', 'storefront', 'mobile'],
                    ['en_US', 'en_US', 'uk_UA'],
                ),
                new CatalogCategoryMediaBindingEntityState(true, true, ['format' => 'webp']),
                new CatalogAuditContext('operator-1', 'bind category banner asset'),
            ),
        )->payload());

        self::assertSame('binding-201', $payload['bindingId']);
        self::assertSame('banner', $payload['role']);
        self::assertSame(['storefront', 'mobile'], $payload['channels']);
        self::assertSame(['en_US', 'uk_UA'], $payload['locales']);
        self::assertTrue($payload['requiredForPublish']);
        self::assertSame('webp', $payload['metadata']['format']);
        self::assertCount(1, $repository->bindingsForCategory('category-701'));
        self::assertCount(1, $repository->history());
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{bindingId: string, role: string, channels: list<string>, locales: list<string>, requiredForPublish: bool, metadata: array{format: string}}
     */
    private function normalizePayload(array $payload): array
    {
        $metadata = is_array($payload['metadata'] ?? null) ? $payload['metadata'] : [];

        return [
            'bindingId' => $this->scalarString($payload['bindingId'] ?? ''),
            'role' => $this->scalarString($payload['role'] ?? ''),
            'channels' => $this->stringList($payload['channels'] ?? []),
            'locales' => $this->stringList($payload['locales'] ?? []),
            'requiredForPublish' => (bool) ($payload['requiredForPublish'] ?? false),
            'metadata' => [
                'format' => $this->scalarString($metadata['format'] ?? ''),
            ],
        ];
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map(fn (mixed $item): string => $this->scalarString($item), $value));
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
