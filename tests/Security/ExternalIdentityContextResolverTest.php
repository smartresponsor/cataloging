<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Security;

use App\Cataloging\Service\Security\ExternalIdentityContextResolver;
use App\Cataloging\ServiceInterface\OidcJwtValidatorInterface;
use App\Cataloging\ServiceInterface\Security\SecurityExternalIdentityContextMapperInterface;
use App\Cataloging\ValueObject\Security\ExternalIdentityContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ExternalIdentityContextResolverTest extends TestCase
{
    public function testResolveReturnsNullWithoutBearerToken(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request());

        $resolver = new ExternalIdentityContextResolver(
            $requestStack,
            new class implements OidcJwtValidatorInterface {
                public function validate(string $jwt): array
                {
                    throw new \RuntimeException('should not be called');
                }
            },
            new class implements SecurityExternalIdentityContextMapperInterface {
                public function map(array $claims): ExternalIdentityContext
                {
                    throw new \RuntimeException('should not be called');
                }
            },
        );

        self::assertNull($resolver->resolveFromCurrentRequest());
    }

    public function testResolveMapsValidatedClaims(): void
    {
        $request = new Request(server: ['HTTP_AUTHORIZATION' => 'Bearer token-123']);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $resolver = new ExternalIdentityContextResolver(
            $requestStack,
            new class implements OidcJwtValidatorInterface {
                public function validate(string $jwt): array
                {
                    TestCase::assertSame('token-123', $jwt);

                    return ['sub' => 'actor-1', 'tenant' => 'tenant-a', 'category_roles' => ['publisher']];
                }
            },
            new class implements SecurityExternalIdentityContextMapperInterface {
                public function map(array $claims): ExternalIdentityContext
                {
                    TestCase::assertSame('actor-1', $claims['sub']);

                    return new ExternalIdentityContext('actor-1', 'tenant-a', ['ROLE_USER'], ['publisher']);
                }
            },
        );

        $context = $resolver->resolveFromCurrentRequest();

        self::assertInstanceOf(ExternalIdentityContext::class, $context);
        self::assertSame('tenant-a', $context->tenant);
        self::assertSame(['publisher'], $context->categoryRoles);
        self::assertSame($context, $request->attributes->get('_catalog_external_identity_context'));
    }
}
