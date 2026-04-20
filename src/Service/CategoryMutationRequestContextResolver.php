<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Resolves request context metadata for category mutation commands.
 */
final readonly class CategoryMutationRequestContextResolver
{
    public function __construct(private Security $security)
    {
    }

    public function actorId(Request $request): string
    {
        $user = $this->security->getUser();
        if ($user instanceof UserInterface) {
            $identifier = trim($user->getUserIdentifier());
            if ('' !== $identifier) {
                return $identifier;
            }
        }

        $headerActorId = trim((string) $request->headers->get('X-Actor-Id', ''));
        if ('' !== $headerActorId) {
            return $headerActorId;
        }

        return 'category-api';
    }

    public function idempotencyKey(Request $request): ?string
    {
        $headerValue = trim((string) $request->headers->get('X-Idempotency-Key', ''));

        return '' !== $headerValue ? $headerValue : null;
    }

    public function correlationId(Request $request): ?string
    {
        $headerValue = trim((string) $request->headers->get('X-Correlation-ID', ''));

        return '' !== $headerValue ? $headerValue : null;
    }
}
