<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface\Security;

interface SecurityJwkCacheInterface
{
    public function getPrivateKey(): string;
}
