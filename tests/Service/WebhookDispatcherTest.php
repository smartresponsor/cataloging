<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Observability\RequestCorrelationIdProvider;
use App\Service\WebhookDispatcher;
use App\ValueObject\WebhookDispatchRequest;
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

        $dispatcher = new WebhookDispatcher($client, 'secret', new RequestCorrelationIdProvider($stack));
        $dispatcher->dispatch(new WebhookDispatchRequest('http://example/webhook', 'catalog.changed', ['id' => 'c-1']));

        self::assertSame('POST', $capturedOptions['method']);
        self::assertSame('http://example/webhook', $capturedOptions['url']);
        self::assertSame(5.0, $capturedOptions['options']['timeout']);
        self::assertSame(
            ['X-Correlation-ID: corr-dispatcher'],
            $capturedOptions['options']['normalized_headers']['x-correlation-id'] ?? null,
        );
        self::assertSame(
            ['X-Category-Event: catalog.changed'],
            $capturedOptions['options']['normalized_headers']['x-category-event'] ?? null,
        );
        self::assertArrayHasKey('x-category-signature', $capturedOptions['options']['normalized_headers']);
    }
}
