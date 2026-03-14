# Category garbage cleanup note

This wave does not hard-delete suspicious files.
It converts obvious garbage candidates into explicit tombstones so IDE inspection and batch cleanup can remove them safely.

Immediate delete candidates:
- src/Service/CanonicalPolicyLocale-.php
- src/Service/CategorySitemapGenerator-.php
- tools/release/make-category-f..p.sh
- report/category-f..p-hashes.txt

Review candidates:
- tools/ops-doc/*.zip
- tools/Category/sketch7/*.zip
- .git/*
- .idea/*
