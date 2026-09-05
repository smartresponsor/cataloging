<?php

declare(strict_types=1);

namespace App\Cataloging\Service\Config;

use App\Administering\Service\Config\AdministrationConfigApplyService;
use App\Administering\Service\Config\AdministrationConfigFileWriterService;
use App\Administering\ServiceInterface\Config\ConfigToolServiceInterface;
use App\Administering\Value\Config\ConfigToolDescriptor;
use App\Cataloging\Form\Config\CatalogingOidcConfigFormType;
use App\Cataloging\Value\Form\Config\CatalogingOidcConfigData;
use Symfony\Component\Yaml\Yaml;

final readonly class CatalogingOidcConfigService implements ConfigToolServiceInterface
{
    public function __construct(
        private string $projectDir,
        private AdministrationConfigApplyService $applyService,
        private AdministrationConfigFileWriterService $fileWriter,
    ) {
    }

    public function descriptor(): ConfigToolDescriptor
    {
        return new ConfigToolDescriptor(
            applicationCode: 'Cataloging',
            toolCode: 'cataloging.oidc',
            label: 'Cataloging OIDC',
            description: 'Safe OIDC trust settings stored in the Cataloging component env manifest.',
            formClass: CatalogingOidcConfigFormType::class,
            serviceClass: self::class,
            requiredPermission: 'administration.config.update',
            editableFields: ['audience', 'issuer', 'jwkSetJson'],
            sensitiveFields: [],
            readableFiles: ['config/component/runtime.yaml'],
            writableFiles: ['config/component/runtime.yaml'],
            metadata: [
                'section' => 'Configuration',
                'kind' => 'oidc',
            ],
            secretNames: [],
            applyStrategy: 'component_yaml',
        );
    }

    public function loadData(): object
    {
        $data = new CatalogingOidcConfigData();
        $manifest = $this->envManifest();

        $data->audience = (string) ($manifest['catalog.oidc_audience'] ?? $data->audience);
        $data->issuer = (string) ($manifest['catalog.oidc_issuer'] ?? $data->issuer);
        $data->jwkSetJson = json_encode($manifest['catalog.oidc_jwk_set'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '[]';

        return $data;
    }

    public function save(object $data, array $context = []): array
    {
        $payload = $this->assertData($data);
        $values = $this->stateRows($payload, 'pending');
        $masked = [
            'catalog.oidc_audience' => $payload->audience,
            'catalog.oidc_issuer' => $payload->issuer,
            'catalog.oidc_jwk_set' => $payload->jwkSetJson,
        ];

        return $this->applyService->save($this->descriptor(), (string) ($context['actor'] ?? 'system'), $values, $masked, []);
    }

    public function apply(object $data, array $context = []): array
    {
        $payload = $this->assertData($data);
        $patch = $this->envPatch($payload);
        $write = $this->fileWriter->write(
            $this->projectDir.'/../Cataloging',
            'config/component/runtime.yaml',
            $patch,
            $this->descriptor()->writableFiles,
        );

        $status = 'applied' === $write['status'] ? 'applied' : 'failed';
        $values = $this->stateRows($payload, $status);

        return $this->applyService->apply(
            $this->descriptor(),
            (string) ($context['actor'] ?? 'system'),
            $values,
            $patch,
            [],
            [[
                'path' => $write['path'],
                'backup_path' => $write['backup_path'],
                'status' => $write['status'],
                'message' => $write['message'],
            ]],
            [],
            'applied' === $write['status'] ? null : $write['message'],
            $status,
        );
    }

    private function assertData(object $data): CatalogingOidcConfigData
    {
        if (!$data instanceof CatalogingOidcConfigData) {
            throw new \InvalidArgumentException('Cataloging OIDC config expects CatalogingOidcConfigData.');
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function envManifest(): array
    {
        $path = $this->projectDir.'/../Cataloging/config/component/runtime.yaml';
        $parsed = is_file($path) ? Yaml::parseFile($path) : [];

        return is_array($parsed) ? $parsed : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function envPatch(CatalogingOidcConfigData $data): array
    {
        $decoded = json_decode($data->jwkSetJson, true);
        if (!is_array($decoded)) {
            throw new \InvalidArgumentException('CATALOG_OIDC_JWK_SET_JSON must be valid JSON array/object.');
        }

        return [
            'parameters' => [
                'catalog.oidc_jwk_set' => $decoded,
                'catalog.oidc_audience' => $data->audience,
                'catalog.oidc_issuer' => $data->issuer,
            ],
            'env' => [
                'component' => 'cataloging',
                'required' => [],
                'optional' => [],
            ],
        ];
    }

    /**
     * @return array<string, array{fieldType:string, secret:bool, current:?string, pending:?string, masked:?string, status:string}>
     */
    private function stateRows(CatalogingOidcConfigData $data, string $status): array
    {
        return [
            'catalog.oidc_audience' => ['fieldType' => 'string', 'secret' => false, 'current' => $data->audience, 'pending' => $data->audience, 'masked' => null, 'status' => $status],
            'catalog.oidc_issuer' => ['fieldType' => 'string', 'secret' => false, 'current' => $data->issuer, 'pending' => $data->issuer, 'masked' => null, 'status' => $status],
            'catalog.oidc_jwk_set' => ['fieldType' => 'textarea', 'secret' => false, 'current' => $data->jwkSetJson, 'pending' => $data->jwkSetJson, 'masked' => null, 'status' => $status],
        ];
    }
}
