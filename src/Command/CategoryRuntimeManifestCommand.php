<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'category:runtime:manifest')]
final class CategoryRuntimeManifestCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = dirname(__DIR__, 2);

        $routeManifest = [
            ['name' => 'api_category_tree', 'path' => '/api/category/tree', 'controller' => 'App\\Controller\\CategoryApiController::tree', 'file' => 'src/Controller/CategoryApiController.php'],
            ['name' => 'api_admin_category_bulk', 'path' => '/api/admin/category/bulk', 'controller' => 'App\\Controller\\Api\\CategoryAdminApiController::bulk', 'file' => 'src/Controller/Api/CategoryAdminApiController.php'],
            ['name' => 'api_category_store', 'path' => '/api/category/store', 'controller' => 'App\\Controller\\CategoryStoreApiController::__invoke', 'file' => 'src/Controller/CategoryStoreApiController.php'],
            ['name' => 'category_storefront', 'path' => '/category/storefront', 'controller' => 'App\\Controller\\CategoryStorefrontController::__invoke', 'file' => 'src/Controller/CategoryStorefrontController.php'],
            ['name' => 'admin_category_move', 'path' => '/admin/category/tree/move', 'controller' => 'App\\Controller\\Admin\\CategoryMoveController::__invoke', 'file' => 'src/Controller/Admin/CategoryMoveController.php'],
        ];

        $commandManifest = [
            ['name' => 'category:publish', 'file' => 'src/Command/PublishCategoryCommand.php'],
            ['name' => 'category:tree:dump', 'file' => 'src/Command/DumpCategoryTreeCommand.php'],
            ['name' => 'category:runtime:manifest', 'file' => 'src/Command/CategoryRuntimeManifestCommand.php'],
            ['name' => 'category:runtime:probe', 'file' => 'src/Command/CategoryRuntimeProbeCommand.php'],
            ['name' => 'category:runtime:closure', 'file' => 'src/Command/CategoryRuntimeClosureCommand.php'],
            ['name' => 'category:runtime:gate', 'file' => 'src/Command/CategoryRuntimeGateCommand.php'],
            ['name' => 'category:runtime:self-check', 'file' => 'src/Command/CategoryRuntimeSelfCheckCommand.php'],
            ['name' => 'category:runtime:release-report', 'file' => 'src/Command/CategoryRuntimeReleaseReportCommand.php'],
            ['name' => 'category:runtime:rc-verdict', 'file' => 'src/Command/CategoryRuntimeRcVerdictCommand.php'],
            ['name' => 'category:runtime:release-envelope', 'file' => 'src/Command/CategoryRuntimeReleaseEnvelopeCommand.php'],
            ['name' => 'app:category:projection:run', 'file' => 'src/Command/RunCategoryProjectionCommand.php'],
        ];

        $contractManifest = [
            ['name' => 'openapi', 'file' => 'api/category-openapi.yaml'],
            ['name' => 'graphql', 'file' => 'config/graphql/category.yaml'],
            ['name' => 'route_move_yaml', 'file' => 'config/routes/category-move.yaml'],
        ];

        $routes = array_map(fn (array $item): array => $this->withExistence($root, $item), $routeManifest);
        $commands = array_map(fn (array $item): array => $this->withExistence($root, $item), $commandManifest);
        $contracts = array_map(fn (array $item): array => $this->withExistence($root, $item), $contractManifest);

        $payload = [
            'ok' => true,
            'routesCount' => count($routes),
            'commandsCount' => count($commands),
            'contractsCount' => count($contracts),
            'allFilesPresent' => $this->allPresent($routes) && $this->allPresent($commands) && $this->allPresent($contracts),
            'routes' => $routes,
            'commands' => $commands,
            'contracts' => $contracts,
        ];

        $output->writeln(json_encode($payload, JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }

    /**
     * @param array{name:string,file:string,path?:string,controller?:string} $item
     *
     * @return array{name:string,file:string,path?:string,controller?:string,exists:bool}
     */
    private function withExistence(string $root, array $item): array
    {
        $item['exists'] = is_file($root.'/'.$item['file']);

        return $item;
    }

    /**
     * @param list<array{exists:bool}> $items
     */
    private function allPresent(array $items): bool
    {
        foreach ($items as $item) {
            if (true !== $item['exists']) {
                return false;
            }
        }

        return true;
    }
}
