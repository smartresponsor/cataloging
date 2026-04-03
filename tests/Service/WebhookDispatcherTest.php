<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Observability\RequestCorrelationIdProvider;
use App\Service\WebhookDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class WebhookDispatcherTest extends TestCase
{
    public function testDispatchAddsSignatureCorrelationIdAndTimeout(): void
    {
        /** @var array{method:string,url:string,options:array{timeout:float,headers:array<string,string>}} $capturedOptions */
        $capturedOptions = [];
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$capturedOptions): MockResponse {
            $capturedOptions = ['method' => $method, 'url' => $url, 'options' => $options];

            return new MockResponse('{}', ['http_code' => 202]);
        });

        $stack = new RequestStack();
        $request = Request::create('/');
        $request->attributes->set(RequestCorrelationIdProvider::ATTRIBUTE, 'corr-dispatcher');
        $stack->push($request);

        $dispatcher = new WebhookDispatcher($client, new RequestCorrelationIdProvider($stack), 'secret');
        $dispatcher->dispatch('catalog.changed', ['id' => 'c-1'], 'http://example/webhook');

        self::assertSame('POST', $capturedOptions['method']);
        self::assertSame('http://example/webhook', $capturedOptions['url']);
        self::assertSame(5.0, $capturedOptions['options']['timeout']);
        self::assertSame('corr-dispatcher', $capturedOptions['options']['headers'][RequestCorrelationIdProvider::HEADER]);
        self::assertSame('catalog.changed', $capturedOptions['options']['headers']['X-Category-Event']);
        self::assertArrayHasKey('X-Category-Signature', $capturedOptions['options']['headers']);
    }
}
