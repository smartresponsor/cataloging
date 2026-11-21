<?php

declare(strict_types=1);

namespace App\Cataloging\ValueObject\Security;

/**
 * Provides the external identity context implementation.
 */
final readonly class ExternalIdentityContext
{
    /**
     * @param list<string> $frameworkRoles
     * @param list<string> $categoryRoles
     */
    public function __construct(
        public string $subject,
        public ?string $tenant,
        public array $frameworkRoles,
        public array $categoryRoles,
    ) {
    }
}
