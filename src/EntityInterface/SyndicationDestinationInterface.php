<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EntityInterface;

/**
 * Defines the contract for category syndication destination.
 */
interface CatalogSyndicationDestinationEntityInterface
{
    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;

    /**
     * Handles the name workflow.
     */
    public function name(): string;

    /**
     * Handles the destination type workflow.
     */
    public function destinationType(): string;

    /**
     * Handles the delivery mode workflow.
     */
    public function deliveryMode(): string;

    /**
     * Handles the enabled workflow.
     */
    public function enabled(): bool;

    /**
     * @return array<string,string>
     */
    public function settings(): array;

    /**
     * Creates the d by result for the current workflow.
     */
    public function createdBy(): string;

    /**
     * Creates the d at result for the current workflow.
     */
    public function createdAt(): \DateTimeImmutable;
}
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationInterface', false)) {
    class_alias(CatalogSyndicationDestinationEntityInterface::class, __NAMESPACE__.'\\SyndicationDestinationInterface');
}
