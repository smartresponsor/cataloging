# Cataloging error surface hardening note

Goal:
- harden raw JSON, file IO, and PDO-heavy surfaces
- avoid silent `@mkdir` and unchecked writes
- return human-readable English messages from controllers and commands
- keep operational details in logs

Applied in this wave:
- storage-backed services
- import/export surfaces
- controller endpoints with write or preview behavior
- projection and redirect storage flows

Follow-up:
- run `php tools/inspection/cataloging-error-surface-audit.php`
- scan remaining raw IO and DB surfaces before the next RC pass
