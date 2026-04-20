<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Observability;

use App\Cataloging\Observability\MonologCorrelationProcessor;
use App\Cataloging\Observability\RequestCorrelationIdProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class MonologCorrelationProcessorTest extends TestCase
{
    public function testAddsCorrelationIdToContextAndExtra(): void
    {
        $stack = new RequestStack();
        $request = new Request();
        $request->attributes->set(RequestCorrelationIdProvider::ATTRIBUTE, 'corr-log-123');
        $stack->push($request);

        $processor = new MonologCorrelationProcessor(new RequestCorrelationIdProvider($stack));

        /** @var array{message:string,context:array<string,string>,extra:array<string,string>} $record */
        $record = $processor([
            'message' => 'category.audit',
            'context' => ['action' => 'publish'],
            'extra' => [],
        ]);

        self::assertSame('corr-log-123', $record['context']['correlation_id']);
        self::assertSame('corr-log-123', $record['extra']['correlation_id']);
    }

    public function testLeavesRecordUntouchedWhenNoCorrelationIdExists(): void
    {
        $processor = new MonologCorrelationProcessor(new RequestCorrelationIdProvider(new RequestStack()));

        $record = [
            'message' => 'category.audit',
            'context' => ['action' => 'publish'],
            'extra' => [],
        ];

        self::assertSame($record, $processor($record));
    }
}
