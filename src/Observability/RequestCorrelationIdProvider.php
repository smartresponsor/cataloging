<?php

declare(strict_types=1);

namespace App\Cataloging\Observability;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides the request correlation id provider implementation.
 */
final readonly class RequestCorrelationIdProvider
{
    public const string ATTRIBUTE = '_catalog_correlation_id';
    public const string HEADER = 'X-Correlation-ID';

    /**
     * Initializes the request correlation id provider service collaborators.
     */
    public function __construct(private RequestStack $requestStack)
    {
    }

    /**
     * Handles the current workflow.
     */
    public function current(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            return null;
        }

        $value = $request->attributes->get(self::ATTRIBUTE);

        return is_string($value) && '' !== $value ? $value : null;
    }
}
