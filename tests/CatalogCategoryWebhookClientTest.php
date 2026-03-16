<?php

declare(strict_types=1);

namespace App\Tests;

use App\Service\Integration\WebhookClient;
use App\Service\Security\JwkCache;
use App\Service\Security\OidcJwtVerifier;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class CatalogCategoryWebhookClientTest extends TestCase
{
    public function testDispatch(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'jwk');
        file_put_contents($tmp, 'private-key');

        $response = $this->createMock(ResponseInterface::class);
        $client = $this->createMock(HttpClientInterface::class);
        $client->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                'http://example/webhook',
                self::callback(static function (array $options): bool {
                    return isset($options['json'], $options['headers']['Authorization'], $options['headers']['X-SR-Source'])
                        && 'category' === $options['headers']['X-SR-Source'];
                })
            )
            ->willReturn($response);

        $verifier = new OidcJwtVerifier(new JwkCache($tmp), 'issuer', 'audience');
        $webhook = new WebhookClient($client, $verifier, 'http://example/webhook');
        $webhook->dispatch(['event' => 'category.changed']);

        @unlink($tmp);
        self::assertTrue(true);
    }
}
