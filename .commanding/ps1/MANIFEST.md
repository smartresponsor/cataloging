PowerShell manifest

Scope
- Root PowerShell scripts are source files.
- Embedded dot copies are runtime projections.
- Do not edit projection copies as if they were the canonical source.

Conventions
- Resolve paths from the root runtime whenever possible.
- Fail early on missing git/bash/wsl runtime dependencies.
- Keep output paths explicit and predictable.
