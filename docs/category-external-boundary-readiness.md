# Category External Boundary Readiness

This component does not own the full authentication platform and does not own the binary attachment engine.

## External auth boundary
- external identity provider remains the system of record;
- Cataloging only verifies and maps trusted claims into local authorization context;
- `ExternalIdentityContextMapper` maps `sub`, tenant claims, framework roles, and category-scoped roles;
- local authorization remains a Cataloging concern.

## External attachment boundary
- Cataloging stores category-to-attachment bindings only;
- it does not store file binaries, process media, or act as the attachment system of record;
- canonical attachment binding fields are:
  - `provider`
  - `external_attachment_id`
  - optional `reference_uri`
- the local service validates bindable references through `AttachmentReferenceGatewayInterface`.

## Readiness expectation
A ready boundary means Cataloging can:
- trust externally validated identity context,
- map external claims into local policy decisions,
- bind external attachments to categories without becoming the media subsystem.
