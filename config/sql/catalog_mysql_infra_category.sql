-- Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
-- Cataloging category projection search indexes.

CREATE INDEX idx_category_projection_name ON category_projection (name_entity);
CREATE INDEX idx_category_projection_tenant_locale ON category_projection (tenant, locale);
CREATE INDEX idx_category_projection_workflow_state ON category_projection (workflow_state);
CREATE INDEX idx_category_projection_updated_at ON category_projection (updated_at);

-- Search filter coverage: name, tenant, locale, workflow_state, updated_at.
