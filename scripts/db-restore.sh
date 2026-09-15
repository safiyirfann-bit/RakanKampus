#!/usr/bin/env bash
# Restores a .dump file (created by db-backup.sh) into a Postgres database.
#
# Usage:
#   DATABASE_URL="postgres://user:pass@host:port/dbname" ./scripts/db-restore.sh backups/rakankampus-20260101-120000.dump
#
# WARNING: this can overwrite existing tables in the target database.

set -euo pipefail

if [ -z "${DATABASE_URL:-}" ]; then
  echo "Error: set DATABASE_URL first (copy 'External Database URL' from Render dashboard)."
  exit 1
fi

FILE="${1:-}"
if [ -z "$FILE" ] || [ ! -f "$FILE" ]; then
  echo "Usage: DATABASE_URL=... ./scripts/db-restore.sh <path-to-dump-file>"
  exit 1
fi

docker run --rm -i -e PGSSLMODE=require postgres:16 \
  pg_restore --no-owner --no-privileges --clean --if-exists -d "$DATABASE_URL" < "$FILE"

echo "Restore complete from $FILE"
