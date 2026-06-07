<?php

declare(strict_types=1);

namespace App\Cataloging\Value\Form\Config;

final class CatalogingOidcConfigData
{
    public string $audience = 'catalog-dev';
    public string $issuer = 'catalog-dev';
    public string $jwkSetJson = '[]';
}
