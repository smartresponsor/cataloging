# Category OIDC runtime proof

This report proves the local OIDC/JWKS verification baseline against a generated RSA key pair and an in-memory JWKS.

## Scope

The proof layer validates that the category OIDC verifier and validator:

- accept a valid RS256 token signed by a JWKS-backed RSA key,
- reject missing issuer claims,
- reject invalid issuer claims,
- reject missing audience claims,
- reject invalid audience claims,
- reject expired tokens,
- reject tokens with future `nbf`,
- reject unknown `kid` values,
- reject unsupported algorithms,
- fail closed when the validator is constructed without a verifier.

## Command

```bash
composer report:oidc-runtime-proof
```

## Output

```text
report/inspection/catalog-oidc-runtime-proof-report.json
```

## RC meaning

A passing report shows that the local OIDC runtime baseline does more than expose documentation and classes; it proves claim validation, key selection, and fail-closed behavior through executable scenarios.
