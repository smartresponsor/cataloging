<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service;

use App\Cataloging\Observability\RequestCorrelationIdProvider;
use App\Cataloging\Service\CatalogWebhookDispatcherService;
use App\Cataloging\ValueObject\WebhookDispatchMessageRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class WebhookDispatcherTest extends TestCase
{
    public function testDispatchAddsSignatureCorrelationIdAndTimeout(): void
    {
        /** @var array{method:string,url:string,options:array{timeout:float,headers:array<string,string>,normalized_headers:array<string,list<string>>}} $capturedOptions */
        $capturedOptions = [];
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$capturedOptions): MockResponse {
            $capturedOptions = ['method' => $method, 'url' => $url, 'options' => $options];

            return new MockResponse('{}', ['http_code' => 202]);
        });

        $stack = new RequestStack();
        $request = Request::create('/');
        $request->attributes->set(RequestCorrelationIdProvider::ATTRIBUTE, 'corr-dispatcher');
        $stack->push($request);

        $dispatcher = new CatalogWebhookDispatcherService($client, 'secret', new RequestCorrelationIdProvider($stack));
        $dispatcher->dispatch(new WebhookDispatchMessageRequest('http://example/webhook', 'catalog.changed', ['id' => 'c-1']));

        self::assertSame('POST', $capturedOptions['method']);
        self::assertSame('http://example/webhook', $capturedOptions['url']);
        self::assertSame(5.0, $capturedOptions['options']['timeout']);
        $normalizedHeaders = $capturedOptions['options']['normalized_headers'] ?? [];
        self::assertIsArray($normalizedHeaders);
        self::assertSame(
            ['X-Correlation-ID: corr-dispatcher'],
            is_array($normalizedHeaders['x-correlation-id'] ?? null) ? $normalizedHeaders['x-correlation-id'] : null,
        );
        self::assertSame(
            ['X-CategoryEntity-Event: catalog.changed'],
            is_array($normalizedHeaders['x-categoryentity-event'] ?? null) ? $normalizedHeaders['x-categoryentity-event'] : null,
        );
        self::assertArrayHasKey('x-categoryentity-signature', $normalizedHeaders);
    }
}
