# Category CLI parity step01

This wave introduces the first operator-facing workflow and review queue commands.

## Commands

- `category:workflow:transition <categoryId> <targetState> <actorId> <reason>`
- `category:review:queue:list <reviewer> [--format=ndjson|json]`

## Intent

Step01 is intentionally narrow:
- expose workflow transition as an operator-safe CLI surface;
- expose reviewer queue inspection as an operator-safe CLI surface;
- do not rewrite existing publish/import/export command flows;
- keep the implementation on the current Symfony-oriented service stack.

## Output shape

### `category:workflow:transition`
Outputs one JSON payload line matching `CategoryWorkflowTransitioned::payload()`.

### `category:review:queue:list`
Supports:
- `ndjson` (default): one queue item per line;
- `json`: one JSON array payload.

Each queue item contains:
- `requestId`
- `categoryId`
- `assignedReviewer`
- `priority`
- `requestState`
- `readyForReview`
- `readinessWarnings`
- `dueAt`
