#!/usr/bin/env bash
set -euo pipefail
BASE_URL="${1:-http://localhost:8080}"
echo "Smoke publish at $BASE_URL"
# Intentionally minimal until API is wired.
exit 0
