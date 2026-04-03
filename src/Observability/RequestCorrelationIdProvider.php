<?php

declare(strict_types=1);

namespace App\Observability;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class RequestCorrelationIdProvider
{
    public const ATTRIBUTE = '_catalog_correlation_id';
    public const HEADER = 'X-Correlation-ID';

    public function __construct(private RequestStack $requestStack)
    {
    }

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
