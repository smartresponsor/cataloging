<?php

declare(strict_types=1);

namespace App\Cataloging\AttachmentInterface;

/**
 * Defines the contract for attachment reference gateway.
 */
interface AttachmentReferenceGatewayInterface
{
    /**
     * Handles the assert bindable workflow.
     */
    public function assertBindable(string $provider, string $externalAttachmentId, ?string $referenceUri = null): void;
}
