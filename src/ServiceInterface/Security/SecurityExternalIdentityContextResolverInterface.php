<?php

declare(strict_types=1);

namespace App\ServiceInterface\Security;

use App\Security\ExternalIdentityContext;
use Symfony\Component\HttpFoundation\Request;

interface SecurityExternalIdentityContextResolverInterface
{
    public function resolveFromCurrentRequest(): ?ExternalIdentityContext;

    public function resolveFromRequest(Request $request): ?ExternalIdentityContext;
}
