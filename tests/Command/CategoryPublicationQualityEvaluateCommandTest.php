<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CategoryPublicationQualityEvaluateCommand;
use App\Policy\CategoryPublicationQualityPolicy;
use App\Service\CategoryPublicationQualityService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoryPublicationQualityEvaluateCommandTest extends TestCase
{
    public function testExecutePrintsQualityPayload(): void
    {
        $service = new CategoryPublicationQualityService(new CategoryPublicationQualityPolicy());
        $command = new CategoryPublicationQualityEvaluateCommand($service);

        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'categoryId' => 'cat-300',
            'score' => '78',
            'actorId' => 'ops.user',
            'reason' => 'cli quality',
            '--publication-checks' => json_encode([
                'slugReady' => true,
                'seoReady' => true,
                'contentReady' => true,
                'localeReady' => true,
                'mediaReady' => false,
                'aliasReady' => false,
            ], JSON_THROW_ON_ERROR),
            '--checks' => json_encode([
                'bannerReady' => false,
                'htmlBlockReady' => true,
            ], JSON_THROW_ON_ERROR),
        ]);

        self::assertSame(0, $exitCode);
        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('cat-300', $payload['categoryId']);
        self::assertTrue($payload['publishableQuality']);
        self::assertSame('attention', $payload['riskLevel']);
    }
}
