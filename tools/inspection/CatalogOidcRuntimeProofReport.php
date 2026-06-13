<?php

declare(strict_types=1);

use App\Cataloging\Service\OidcJwtVerifier;
use App\Cataloging\Service\OidcJwtValidator;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$root = dirname(__DIR__, 2);
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-oidc-runtime-proof-report.json';

/**
 * @return array{privateKey:OpenSSLAsymmetricKey,jwkSet:array{keys:list<array<string,string>>}}
 */
function generateJwkFixture(): array
{
    $privateKey = openssl_pkey_new([
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ]);
    if (!$privateKey instanceof OpenSSLAsymmetricKey) {
        throw new RuntimeException('Unable to generate RSA private key.');
    }

    $details = openssl_pkey_get_details($privateKey);
    if (!is_array($details) || !isset($details['rsa']) || !is_array($details['rsa'])) {
        throw new RuntimeException('Unable to read RSA key details.');
    }
    $rsa = $details['rsa'];
    $n = $rsa['n'] ?? null;
    $e = $rsa['e'] ?? null;
    if (!is_string($n) || !is_string($e)) {
        throw new RuntimeException('RSA key details are incomplete.');
    }

    return [
        'privateKey' => $privateKey,
        'jwkSet' => [
            'keys' => [[
                'kty' => 'RSA',
                'kid' => 'test-kid-1',
                'n' => b64uEncode($n),
                'e' => b64uEncode($e),
            ]],
        ],
    ];
}

function b64uEncode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

/**
 * @param array<string,mixed> $header
 * @param array<string,mixed> $payload
 */
function buildJwt(OpenSSLAsymmetricKey $privateKey, array $header, array $payload): string
{
    $encodedHeader = b64uEncode(json_encode($header, JSON_THROW_ON_ERROR));
    $encodedPayload = b64uEncode(json_encode($payload, JSON_THROW_ON_ERROR));
    $signingInput = $encodedHeader . '.' . $encodedPayload;
    if (!openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        throw new RuntimeException('Unable to sign JWT.');
    }

    return $signingInput . '.' . b64uEncode($signature);
}

/**
 * @return array{status:string,message:string,payload?:array<string,mixed>}
 */
function runScenario(string $nameEntity, callable $scenario): array
{
    try {
        $result = $scenario();
        $details = ['status' => 'pass', 'message' => 'Scenario passed.'];
        if (is_array($result)) {
            $details['payload'] = $result;
        }

        return $details;
    } catch (Throwable $throwable) {
        return [
            'status' => 'fail',
            'message' => sprintf('%s: %s', $nameEntity, $throwable->getMessage()),
        ];
    }
}

/**
 * @return array{status:string,message:string}
 */
function expectExceptionScenario(string $nameEntity, callable $scenario, string $expectedFragment): array
{
    try {
        $scenario();

        return [
            'status' => 'fail',
            'message' => sprintf('%s: expected exception containing "%s".', $nameEntity, $expectedFragment),
        ];
    } catch (Throwable $throwable) {
        $message = $throwable->getMessage();

        return [
            'status' => str_contains($message, $expectedFragment) ? 'pass' : 'fail',
            'message' => sprintf('%s: %s', $nameEntity, $message),
        ];
    }
}

$fixture = generateJwkFixture();
$issuer = 'https://issuer.example.test';
$audience = 'catalog-api';
$verifier = new OidcJwtVerifier($issuer, $audience, $fixture['jwkSet']);
$validator = new OidcJwtValidator($verifier);
$now = time();
$basePayload = [
    'sub' => 'user-123',
    'iss' => $issuer,
    'aud' => [$audience, 'catalog-ui'],
    'exp' => $now + 600,
    'nbf' => $now - 10,
    'iat' => $now - 10,
];
$baseHeader = [
    'alg' => 'RS256',
    'typ' => 'JWT',
    'kid' => 'test-kid-1',
];

$items = [];
$validJwt = buildJwt($fixture['privateKey'], $baseHeader, $basePayload);
$items[] = ['check' => 'valid-token-verifies', 'result' => runScenario('valid-token-verifies', static fn (): array => $validator->validate($validJwt))];
$items[] = ['check' => 'missing-issuer-rejected', 'result' => expectExceptionScenario('missing-issuer-rejected', static function () use ($fixture, $baseHeader, $basePayload, $validator): array { $payload = $basePayload; unset($payload['iss']); return $validator->validate(buildJwt($fixture['privateKey'], $baseHeader, $payload)); }, 'Missing issuer')];
$items[] = ['check' => 'invalid-issuer-rejected', 'result' => expectExceptionScenario('invalid-issuer-rejected', static function () use ($fixture, $baseHeader, $basePayload, $validator): array { $payload = $basePayload; $payload['iss'] = 'https://other-issuer.example.test'; return $validator->validate(buildJwt($fixture['privateKey'], $baseHeader, $payload)); }, 'Invalid issuer')];
$items[] = ['check' => 'missing-audience-rejected', 'result' => expectExceptionScenario('missing-audience-rejected', static function () use ($fixture, $baseHeader, $basePayload, $validator): array { $payload = $basePayload; unset($payload['aud']); return $validator->validate(buildJwt($fixture['privateKey'], $baseHeader, $payload)); }, 'Missing audience')];
$items[] = ['check' => 'invalid-audience-rejected', 'result' => expectExceptionScenario('invalid-audience-rejected', static function () use ($fixture, $baseHeader, $basePayload, $validator): array { $payload = $basePayload; $payload['aud'] = ['catalog-worker']; return $validator->validate(buildJwt($fixture['privateKey'], $baseHeader, $payload)); }, 'Invalid audience')];
$items[] = ['check' => 'expired-token-rejected', 'result' => expectExceptionScenario('expired-token-rejected', static function () use ($fixture, $baseHeader, $basePayload, $validator, $now): array { $payload = $basePayload; $payload['exp'] = $now - 5; return $validator->validate(buildJwt($fixture['privateKey'], $baseHeader, $payload)); }, 'Token expired')];
$items[] = ['check' => 'future-token-rejected', 'result' => expectExceptionScenario('future-token-rejected', static function () use ($fixture, $baseHeader, $basePayload, $validator, $now): array { $payload = $basePayload; $payload['nbf'] = $now + 300; return $validator->validate(buildJwt($fixture['privateKey'], $baseHeader, $payload)); }, 'Token not yet valid')];
$items[] = ['check' => 'unknown-kid-rejected', 'result' => expectExceptionScenario('unknown-kid-rejected', static function () use ($fixture, $baseHeader, $basePayload, $validator): array { $header = $baseHeader; $header['kid'] = 'unknown-kid'; return $validator->validate(buildJwt($fixture['privateKey'], $header, $basePayload)); }, 'Unknown kid')];
$items[] = ['check' => 'unsupported-alg-rejected', 'result' => expectExceptionScenario('unsupported-alg-rejected', static function () use ($fixture, $baseHeader, $basePayload, $validator): array { $header = $baseHeader; $header['alg'] = 'HS256'; return $validator->validate(buildJwt($fixture['privateKey'], $header, $basePayload)); }, 'Unsupported alg')];
$items[] = ['check' => 'validator-without-verifier-fails-closed', 'result' => expectExceptionScenario('validator-without-verifier-fails-closed', static function () use ($validJwt): array { $validator = new OidcJwtValidator(); return $validator->validate($validJwt); }, 'OIDC JWT verifier is not configured.')];

$summary = ['pass' => 0, 'warn' => 0, 'fail' => 0];
foreach ($items as $item) {
    ++$summary[$item['result']['status']];
}
$overallStatus = $summary['fail'] > 0 ? 'fail' : ($summary['warn'] > 0 ? 'warn' : 'pass');

$report = [
    'generatedAt' => date(DATE_ATOM),
    'overallStatus' => $overallStatus,
    'summary' => $summary,
    'issuer' => $issuer,
    'audience' => $audience,
    'items' => $items,
];

file_put_contents($out, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
printf("[CatalogOidcRuntimeProofReport] status=%s pass=%d warn=%d fail=%d written to %s\n", $overallStatus, $summary['pass'], $summary['warn'], $summary['fail'], str_replace($root . DIRECTORY_SEPARATOR, '', $out));
