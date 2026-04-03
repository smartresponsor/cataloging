<?php

declare(strict_types=1);

namespace App\Attachment;

use App\AttachmentInterface\AttachmentReferenceGatewayInterface;

final class NullAttachmentReferenceGateway implements AttachmentReferenceGatewayInterface
{
    public function assertBindable(string $provider, string $externalAttachmentId, ?string $referenceUri = null): void
    {
        $normalizedProvider = trim($provider);
        $normalizedAttachmentId = trim($externalAttachmentId);
        $normalizedReferenceUri = null === $referenceUri ? null : trim($referenceUri);

        if ('' === $normalizedProvider) {
            throw new \InvalidArgumentException('provider is required');
        }
        if ('' === $normalizedAttachmentId) {
            throw new \InvalidArgumentException('external_attachment_id is required');
        }
        if (null !== $normalizedReferenceUri && '' !== $normalizedReferenceUri && !filter_var($normalizedReferenceUri, FILTER_VALIDATE_URL) && !str_starts_with($normalizedReferenceUri, '/')) {
            throw new \InvalidArgumentException('reference_uri must be an absolute URL or an application-relative path');
        }
    }
}
