<?php

declare(strict_types=1);

namespace App\ServiceInterface\Security;

use App\Security\ExternalIdentityContext;
/**
 * Defines the contract for security external identity context mapper.
 */
interface SecurityExternalIdentityContextMapperInterface
{
    /** @param array<string,mixed> $claims */
    public function map(array $claims): ExternalIdentityContext;
}
