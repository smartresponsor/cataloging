# Cataloging wave 6 semantic tail collapse

This wave removes the last duplicate basename groups by introducing more precise owner names.

Applied renames:
- `Category` command service -> `CategoryCommandService`
- `CategoryInterface` command contract -> `CategoryCommandServiceInterface`
- query read contract `CategoryRepositoryInterface` -> `CategoryListRepositoryInterface`
- entity-style query repository `CategoryRepository` -> `CategoryEntityRepositoryInterface`
- loop runner `CategoryProjectionRunner` -> `CategoryProjectionLoopRunner`
- runner contract `CategoryProjectionRunnerInterface` -> `CategoryProjectionLoopRunnerInterface`
- workflow `CacheInvalidator` -> `CategoryWorkflowCacheInvalidator`
- API `CategoryResolver` -> `CategoryNodeResolver`

Also:
- removed the last placeholder-like assertion in `TreeOperationTest`
