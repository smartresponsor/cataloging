# Cataloging wave 023 — Infrastructure bridge

Goal:
- make `src/Infrastructure/*` the canonical owner for category infrastructure services
- leave `src/Infra/*` as temporary compatibility wrappers

Moved as canonical owners:
- CacheInvalidator
- CategoryAuditLogger
- HttpWebhookSender
- MessengerOutboxDispatcher
- OrderWebhookPublisher
- ProductWebhookPublisher
