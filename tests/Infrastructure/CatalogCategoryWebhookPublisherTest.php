<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure;

use App\Infrastructure\ProductWebhookPublisher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class CatalogtestsWebhookPublisherTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $client = $this->createMock(HttpClientInterface::class);
        $client->expects(self::once())
            ->method('request')
            ->with('POST', 'http://example', ['json' => ['event' => 'category.changed']])
            ->willReturn($response);

        $publisher = new ProductWebhookPublisher($client, 'http://example');
        $publisher->publish(['event' => 'category.changed']);

        self::assertTrue(true);
    }
}
