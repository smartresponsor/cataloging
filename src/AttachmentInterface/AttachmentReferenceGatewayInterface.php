<?php

declare(strict_types=1);

namespace App\AttachmentInterface;

interface AttachmentReferenceGatewayInterface
{
    public function assertBindable(string $provider, string $externalAttachmentId, ?string $referenceUri = null): void;
}
