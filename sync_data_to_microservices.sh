#!/bin/bash
set -e

export PGPASSWORD='T@n12089'
MONO_DB="psql -h 127.0.0.1 -U hadez -d bizmark_db"
CONTAINER_DB="docker exec -i ms_postgres psql -U bizmark_ms"

echo "╔════════════════════════════════════════════════════════╗"
echo "║   DATA SYNC: Monolith → Microservices                 ║"
echo "╚════════════════════════════════════════════════════════╝"

# ── Content/SEO Service ──────────────────────────────────────
echo ""
echo "=== Content/SEO Service ==="
$MONO_DB -c "\COPY (SELECT * FROM article_topics ORDER BY id) TO '/tmp/article_topics.csv' WITH (FORMAT CSV, HEADER true)"
docker cp /tmp/article_topics.csv ms_postgres:/tmp/
$CONTAINER_DB -d bizmark_content_dev << 'EOF'
ALTER TABLE article_topics DISABLE TRIGGER ALL;
\COPY article_topics FROM '/tmp/article_topics.csv' WITH (FORMAT CSV, HEADER true)
ALTER TABLE article_topics ENABLE TRIGGER ALL;
EOF
echo "  ✅ article_topics imported: $($CONTAINER_DB -d bizmark_content_dev -t -c "SELECT COUNT(*) FROM article_topics;")"

# Keyword clusters
$MONO_DB -t -c "SELECT COUNT(*) FROM keyword_clusters;" | grep -q " 0" || {
  $MONO_DB -c "\COPY (SELECT * FROM keyword_clusters ORDER BY id) TO '/tmp/keyword_clusters.csv' WITH (FORMAT CSV, HEADER true)"
  docker cp /tmp/keyword_clusters.csv ms_postgres:/tmp/
  $CONTAINER_DB -d bizmark_content_dev -c "\COPY keyword_clusters FROM '/tmp/keyword_clusters.csv' WITH (FORMAT CSV, HEADER true)"
  echo "  ✅ keyword_clusters imported"
}

# Auto post configs
$MONO_DB -t -c "SELECT COUNT(*) FROM auto_post_configs;" | grep -q " 0" || {
  $MONO_DB -c "\COPY (SELECT * FROM auto_post_configs ORDER BY id) TO '/tmp/auto_post_configs.csv' WITH (FORMAT CSV, HEADER true)"
  docker cp /tmp/auto_post_configs.csv ms_postgres:/tmp/
  $CONTAINER_DB -d bizmark_content_dev -c "ALTER TABLE auto_post_configs DISABLE TRIGGER ALL; \COPY auto_post_configs FROM '/tmp/auto_post_configs.csv' WITH (FORMAT CSV, HEADER true); ALTER TABLE auto_post_configs ENABLE TRIGGER ALL;"
  echo "  ✅ auto_post_configs imported"
}

# ── Auth Service ─────────────────────────────────────────────
echo ""
echo "=== Auth Service ==="
# Users sudah punya 3 dari seed, tambahkan 2 lagi dari monolith (id 4,5)
$MONO_DB -c "\COPY (SELECT id,name,email,'user' as role,created_at,updated_at FROM users WHERE id > 3 ORDER BY id) TO '/tmp/users_new.csv' WITH (FORMAT CSV, HEADER true)"
docker cp /tmp/users_new.csv ms_postgres:/tmp/
$CONTAINER_DB -d bizmark_auth_dev -c "\COPY users (id,name,email,role,created_at,updated_at) FROM '/tmp/users_new.csv' WITH (FORMAT CSV, HEADER true)" 2>/dev/null || echo "  ℹ️  users already exist or error"
echo "  ✅ users total: $($CONTAINER_DB -d bizmark_auth_dev -t -c "SELECT COUNT(*) FROM users;")"

# ── Perizinan Service ────────────────────────────────────────
echo ""
echo "=== Perizinan Service ==="
# KBLI seeder (1700+ records dari GitHub atau CSV public)
echo "  ℹ️  KBLI seeder dari GitHub (skipped jika sudah 30 records)"

# ── HRM Service ──────────────────────────────────────────────
echo ""
echo "=== HRM Service ==="
# Test templates jika ada
$MONO_DB -t -c "SELECT COUNT(*) FROM test_templates;" | grep -q " 0" && echo "  ℹ️  No test templates in monolith" || {
  $MONO_DB -c "\COPY (SELECT * FROM test_templates ORDER BY id) TO '/tmp/test_templates.csv' WITH (FORMAT CSV, HEADER true)"
  docker cp /tmp/test_templates.csv ms_postgres:/tmp/
  $CONTAINER_DB -d bizmark_hrm_dev -c "ALTER TABLE test_templates DISABLE TRIGGER ALL; \COPY test_templates FROM '/tmp/test_templates.csv' WITH (FORMAT CSV, HEADER true); ALTER TABLE test_templates ENABLE TRIGGER ALL;" 2>/dev/null || echo "  ⚠️  test_templates import error"
}

# ── Summary ──────────────────────────────────────────────────
echo ""
echo "╔════════════════════════════════════════════════════════╗"
echo "║   SYNC SUMMARY                                         ║"
echo "╚════════════════════════════════════════════════════════╝"
$CONTAINER_DB -d bizmark_content_dev -t -c "SELECT 'articles' as tbl, COUNT(*) FROM articles UNION ALL SELECT 'article_topics', COUNT(*) FROM article_topics;"
$CONTAINER_DB -d bizmark_auth_dev -t -c "SELECT 'users', COUNT(*) FROM users;"
$CONTAINER_DB -d bizmark_perizinan_dev -t -c "SELECT 'kblis', COUNT(*) FROM kblis;"
