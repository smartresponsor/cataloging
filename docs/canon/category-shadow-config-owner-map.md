# Category shadow config owner map

Canonical config owner paths:
- config/packages/*
- config/routes/*
- config/sql/*
- config/services_graphql.yaml
- config/webhook.sample.json

Deprecated shadow root:
- config/config/*

Policy:
- no new files may be added under config/config
- existing shadow files should be deleted after reference scan
- runtime and tooling must read canonical config/* only
