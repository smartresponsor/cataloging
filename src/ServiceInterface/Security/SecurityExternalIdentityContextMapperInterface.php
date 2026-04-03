<?php

declare(strict_types=1);

namespace App\ServiceInterface\Security;

use App\Security\ExternalIdentityContext;

interface SecurityExternalIdentityContextMapperInterface
{
    /** @param array<string,mixed> $claims */
    public function map(array $claims): ExternalIdentityContext;
}
