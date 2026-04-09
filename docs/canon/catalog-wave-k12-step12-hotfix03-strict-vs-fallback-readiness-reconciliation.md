# K12 Step12 Hotfix03

Aligned strict destination media readiness with fallback semantics.

- strict destination publishability now requires exact channel/locale match
- shared/global bindings remain available only through fallback-aware evaluation
- this reconciles fallback, preference, and fallback-aware package gate tests
