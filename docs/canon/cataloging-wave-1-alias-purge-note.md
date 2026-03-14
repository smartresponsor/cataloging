# Cataloging wave 1 alias purge

Scope:
- remove safe wrapper duplicates under `src/[Layer]/Category/**`
- keep semantic-conflict wrappers for a later collapse wave

Moved to canonical root owners:
- Controller / ControllerInterface
- DataFixtures
- EntityInterface
- Event / EventInterface / EventSubscriber
- Exception
- Exporter / ExporterInterface
- GraphQl (safe subset only)
- Idempotency / IdempotencyInterface
- Importer / ImporterInterface
- Infrastructure / InfrastructureInterface
- Logging
- Observability
- Outbox / OutboxInterface
- Policy / PolicyInterface
- ProjectionInterface
- RepositoryInterface
- Request
- Runner / RunnerInterface
- ValueObject / ValueObjectInterface
- Webhook
- Audit / Cache / CacheInterface / Command (safe duplicate wrappers)

Skipped for next semantic wave:
- `src/Event/CategoryMoved.php`
- `src/GraphQl/CategoryQuery.php`
- `src/GraphQl/CategoryStateProvider.php`
- `src/Projection/CategoryProjectionRunner.php`
- `src/Repository/CategoryRepository.php`
- `src/Security/CategoryVoter.php`
