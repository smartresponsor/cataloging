# Catalog Wave A — Service wrapper evacuation

This wave removes the non-canonical `Category` wrapper directory directly under:

- `src/Service/Category/**`
- `src/ServiceInterface/Category/**`

Canonical result:
- files move to `src/Service/**`
- files move to `src/ServiceInterface/**`

No semantic class renaming is introduced in this wave.
Only structural evacuation and namespace alignment are applied.

Pre-delete before overlay:
- `src/Service/Category`
- `src/ServiceInterface/Category`
