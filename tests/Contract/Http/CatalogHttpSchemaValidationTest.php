<?php

declare(strict_types=1);

namespace App\Tests\Contract\Http;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CatalogHttpSchemaValidationTest extends WebTestCase
{
    use OpenApiResponseSchemaAssertionTrait;

    public function testAttachmentListMatchesSchema(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/category/attachment');

        self::assertResponseIsSuccessful();

        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertPayloadMatchesSchema($payload, 'AttachmentListResponse');
    }

    public function testCollectionBuildMatchesSchema(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/category/collection',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([], JSON_THROW_ON_ERROR),
        );

        self::assertResponseIsSuccessful();

        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertPayloadMatchesSchema($payload, 'CollectionBuildResponse');
    }

    public function testVirtualPreviewMatchesSchema(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/category/virtual/preview',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([], JSON_THROW_ON_ERROR),
        );

        self::assertResponseIsSuccessful();

        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertPayloadMatchesSchema($payload, 'CollectionBuildResponse');
    }
}
