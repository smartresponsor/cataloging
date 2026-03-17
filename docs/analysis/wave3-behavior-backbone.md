# Wave 3 behavior backbone

## Added truth tests

- CategoryApiController direct response tests for tree/move/publish
- WebhookDispatcher signed request contract test
- MessengerOutboxDispatcher bus handoff test
- CategoryProjectionRunner metric reset test

## Hidden seam repairs

- Projection runner now imports CatalogProjectionMetrics explicitly
- CategoryReadController imports CategoryRepository explicitly
- Service\WebhookClient now imports WebhookClientInterface explicitly

## Interim verdict

The component still is not a full release candidate, but the proof surface is wider: transport/controller, webhook signing, outbox dispatch, and projection metrics now have executable truth seams rather than only documentation or placeholders.
