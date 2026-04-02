<?php

declare(strict_types=1);

namespace App\Tests\Contract\Http;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CatalogHttpContractTest extends WebTestCase
{
    public function testOpenApiJsonEndpointRespondsSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/doc.json');

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');

        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($payload);
        self::assertSame('3.0.3', $payload['openapi'] ?? null);
        self::assertSame('Catalog API', $payload['info']['title'] ?? null);
    }

    public function testAttachmentCreateRejectsInvalidPayloadWithCanonicalErrorEnvelope(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/category/attachment',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['type' => 'icon'], JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(400);

        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertFalse($payload['ok'] ?? true);
        self::assertIsArray($payload['errors'] ?? null);
        self::assertContains('category_id is required', $payload['errors']);
        self::assertContains('path is required', $payload['errors']);
    }

    public function testCollectionBuildRejectsMalformedJsonWithCanonicalErrorEnvelope(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/category/collection',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{invalid-json',
        );

        self::assertResponseStatusCodeSame(400);

        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertFalse($payload['ok'] ?? true);
        self::assertSame(['rules payload must be a JSON object or array'], $payload['errors'] ?? null);
    }

    public function testVirtualPreviewRejectsMalformedJsonWithCanonicalErrorEnvelope(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/category/virtual/preview',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: '{invalid-json',
        );

        self::assertResponseStatusCodeSame(400);

        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertFalse($payload['ok'] ?? true);
        self::assertSame(['rules payload must be a JSON object or array'], $payload['errors'] ?? null);
    }
}
