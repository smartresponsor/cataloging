# Wave 18 — runtime probe closure

This wave adds a runtime probe entrypoint that reads persisted repository state and reports:
- public tree truth
- draft visibility gap
- runtime file presence
- runtime markers across commands/controllers/contracts

It tightens the RC story by giving a single runtime command that can prove the contour after persistence-backed mutations.
