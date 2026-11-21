<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Security;

use App\Cataloging\ValueObject\Security\ExternalIdentityContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines the contract for security external identity context resolver.
 */
interface SecurityExternalIdentityContextResolverInterface
{
    /**
     * Resolves the from current request result for the current workflow.
     */
    public function resolveFromCurrentRequest(): ?ExternalIdentityContext;

    /**
     * Resolves the from request result for the current workflow.
     */
    public function resolveFromRequest(Request $request): ?ExternalIdentityContext;
}
