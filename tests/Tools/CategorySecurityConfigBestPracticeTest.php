<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Tests\Tools;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class CategorySecurityConfigBestPracticeTest extends TestCase
{
    public function testCatalogSecurityHasCoreSymfonySecurityHardeningOptions(): void
    {
        $config = Yaml::parseFile(dirname(__DIR__, 2).'/config/packages/catalog_security.yaml');
        $security = $config['security'] ?? [];

        self::assertSame(true, $security['enable_authenticator_manager'] ?? null);
        self::assertSame(true, $security['erase_credentials'] ?? null);
        self::assertSame('unanimous', $security['access_decision_manager']['strategy'] ?? null);
        self::assertSame(false, $security['access_decision_manager']['allow_if_all_abstain'] ?? null);
        self::assertSame(false, $security['access_decision_manager']['allow_if_equal_granted_denied'] ?? null);
        self::assertSame(
            'auto',
            $security['password_hashers']['Symfony\\Component\\Security\\Core\\User\\PasswordAuthenticatedUserInterface'] ?? null
        );
    }

    public function testServiceConfigurationsUseSymfonyDiDefaults(): void
    {
        $infra = Yaml::parseFile(dirname(__DIR__, 2).'/config/packages/catalog_category_infra.yaml');
        $graphql = Yaml::parseFile(dirname(__DIR__, 2).'/config/catalog_category_services_graphql.yaml');

        self::assertSame(true, $infra['services']['_defaults']['autowire'] ?? null);
        self::assertSame(true, $infra['services']['_defaults']['autoconfigure'] ?? null);
        self::assertSame(false, $infra['services']['_defaults']['public'] ?? null);

        self::assertSame(true, $graphql['services']['_defaults']['autowire'] ?? null);
        self::assertSame(true, $graphql['services']['_defaults']['autoconfigure'] ?? null);
        self::assertSame(false, $graphql['services']['_defaults']['public'] ?? null);
    }
}
