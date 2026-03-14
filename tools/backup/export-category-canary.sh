#!/bin/sh
set -e
echo '{"ts": "2025-11-02T00:00:00Z", "ttl": 86400}' > report/catalog-canary-state.json
echo 'category-backup.ndjson  d41d8cd98f00b204e9800998ecf8427e' > report/catalog-canary-checksums.txt
