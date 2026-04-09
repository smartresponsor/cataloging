# Catalog wave K13 step02 - CLI review assignment and quality evaluation

## Scope

This step adds operator CLI commands for:

- assigning reviewers to category change requests
- evaluating category completeness
- evaluating publication quality

## Canon notes

- kept under `src/Command`
- no Port/Adaptor/Hexagonal additions
- commands delegate to existing services and print normalized JSON payloads
