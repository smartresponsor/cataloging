#!/bin/sh
set -e
FILE=report/catalog-telemetry.ndjson
if [ -f "$FILE" ]; then mv "$FILE" "${FILE}.1"; fi
echo '{}' > "$FILE"
