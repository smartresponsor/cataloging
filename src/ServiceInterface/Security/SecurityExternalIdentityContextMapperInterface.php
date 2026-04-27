<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Security;

use App\Cataloging\ValueObject\Security\ExternalIdentityContext;

/**
 * Defines the contract for security external identity context mapper.
 */
interface SecurityExternalIdentityContextMapperInterface
{
    /** @param array<string,mixed> $claims */
    public function map(array $claims): ExternalIdentityContext;
}
