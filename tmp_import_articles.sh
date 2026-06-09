#!/bin/bash
set -e

# Disable FK, import, re-enable
docker exec ms_postgres psql -U bizmark_ms -d bizmark_content_dev -c "ALTER TABLE articles DISABLE TRIGGER ALL;"

docker exec -i ms_postgres psql -U bizmark_ms -d bizmark_content_dev << 'EOF'
\COPY articles FROM '/tmp/articles_export.sql' WITH (FORMAT CSV, HEADER true)
EOF

docker exec ms_postgres psql -U bizmark_ms -d bizmark_content_dev -c "ALTER TABLE articles ENABLE TRIGGER ALL;"
docker exec ms_postgres psql -U bizmark_ms -d bizmark_content_dev -t -c "SELECT COUNT(*) FROM articles;"
