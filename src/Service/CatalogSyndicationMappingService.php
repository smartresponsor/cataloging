<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationPublishPackageBuilt;
use App\EventInterface\CategorySyndicationPublishPackageBuiltInterface;
use App\PolicyInterface\CategorySyndicationMappingPolicyInterface;
use App\ServiceInterface\CatalogSyndicationMappingServiceInterface;
use App\ValueObject\CategorySyndicationMappingProfile;
use App\ValueObject\CategorySyndicationPublishPackage;

/**
 * Provides the catalog syndication mapping service application service.
 */
final readonly class CatalogSyndicationMappingService implements CatalogSyndicationMappingServiceInterface
{
    /**
     * Initializes the catalog syndication mapping service service collaborators.
     */
    public function __construct(
        private CategorySyndicationMappingPolicyInterface $policy,
    ) {
    }

    /**
     * @param array<string,mixed> $categoryData
     * @param array<string,mixed> $fieldMap
     * @param list<string>        $requiredFields
     */
    public function buildPublishPackage(
        string $packageId,
        string $destinationId,
        string $categoryId,
        string $version,
        string $localeMode,
        array $categoryData,
        array $fieldMap,
        array $requiredFields,
        string $actorId,
        string $reason,
    ): CategorySyndicationPublishPackageBuiltInterface {
        $this->policy->assertLocaleMode($localeMode);
        $normalizedFieldMap = $this->policy->normalizeFieldMap($this->normalizeFieldMap($fieldMap));
        $normalizedRequiredFields = $this->policy->normalizeRequiredFields($requiredFields);

        $profile = new CategorySyndicationMappingProfile(
            trim($destinationId),
            trim($version),
            $normalizedFieldMap,
            $normalizedRequiredFields,
            trim($localeMode),
        );

        $payload = [];
        foreach ($profile->fieldMap() as $sourceField => $targetField) {
            $payload[$targetField] = $categoryData[$sourceField] ?? null;
        }

        $missingRequiredFields = [];
        foreach ($profile->requiredFields() as $requiredField) {
            $mappedValue = $payload[$requiredField] ?? null;
            if (null === $mappedValue || '' === $this->stringOrEmpty($mappedValue)) {
                $missingRequiredFields[] = $requiredField;
            }
        }

        $package = new CategorySyndicationPublishPackage(
            trim($packageId),
            $profile->destinationId(),
            trim($categoryId),
            $profile->version(),
            $profile->localeMode(),
            $payload,
            $missingRequiredFields,
            [] === $missingRequiredFields,
        );

        return new CategorySyndicationPublishPackageBuilt(
            [
                'packageId' => $package->packageId(),
                'destinationId' => $package->destinationId(),
                'categoryId' => $package->categoryId(),
                'version' => $package->version(),
                'localeMode' => $package->localeMode(),
                'payload' => $package->payload(),
                'missingRequiredFields' => $package->missingRequiredFields(),
                'publishable' => $package->publishable(),
                'fieldMap' => $profile->fieldMap(),
                'requiredFields' => $profile->requiredFields(),
                'actorId' => trim($actorId),
                'reason' => trim($reason),
            ],
            new \DateTimeImmutable('now'),
        );
    }

    private function stringOrEmpty(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    /**
     * @param array<string,mixed> $fieldMap
     *
     * @return array<string,string>
     */
    private function normalizeFieldMap(array $fieldMap): array
    {
        $normalized = [];
        foreach ($fieldMap as $sourceField => $targetField) {
            if (!is_string($sourceField) || !is_scalar($targetField)) {
                continue;
            }
            $normalized[$sourceField] = (string) $targetField;
        }

        return $normalized;
    }
}
