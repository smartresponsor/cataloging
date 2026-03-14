# Cataloging wave 072

- Move `src/Service/Category/**` to `src/Service/<Direction>/Category/**`.
- Move `src/ServiceInterface/Category/**` to `src/ServiceInterface/<Direction>/Category/**`.
- Flatten `src/Entity/Category/**` to `src/Entity/**`.
- Rewrite internal FQCN references to the new canonical layout.
- Remove legacy alias bridges from the moved canonical files.
