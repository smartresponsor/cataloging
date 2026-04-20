<?php

declare(strict_types=1);

namespace App\Cataloging;

use App\Cataloging\DependencyInjection\CatalogingExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle facade for the Cataloging RC component.
 *
 * The component remains responsible for its own business surface.
 * The host application only enables this bundle and imports routes when needed.
 */
final class CatalogingBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        $extension = parent::getContainerExtension();

        return $extension instanceof ExtensionInterface ? $extension : new CatalogingExtension();
    }
}
