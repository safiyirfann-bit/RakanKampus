#!/usr/bin/env bash
# Backs up the Render Postgres database to a local .dump file.
#
# Usage:
#   DATABASE_URL="postgres://user:pass@host:port/dbname" ./scripts/db-backup.sh
#
# Get DATABASE_URL from: Render dashboard -> rakankampus-db -> "External Database URL".
# Runs pg_dump via Docker so you don't need Postgres client tools installed locally.

set -euo pipefail

if [ -z "${DATABASE_URL:-}" ]; then
  echo "Error: set DATABASE_URL first (copy 'External Database URL' from Render dashboard)."
  exit 1
fi

mkdir -p backups
OUT="backups/rakankampus-$(date +%Y%m%d-%H%M%S).dump"

docker run --rm -e PGSSLMODE=require postgres:16 \
  pg_dump --format=custom --no-owner --no-privileges "$DATABASE_URL" > "$OUT"

echo "Backup saved to $OUT"
