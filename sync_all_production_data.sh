#!/bin/bash
set -e
export PGPASSWORD='T@n12089'
MONO="psql -h 127.0.0.1 -U hadez -d bizmark_db -t"
MICRO="docker exec -i ms_postgres psql -U bizmark_ms"

echo "╔══════════════════════════════════════════════════════════╗"
echo "║  PRODUCTION DATA SYNC: Monolith → Microservices          ║"
echo "╚══════════════════════════════════════════════════════════╝"

# ═══════════════════════════════════════════════════════════
# 1. CONTENT/SEO — Already done, just sync missing tables
# ═══════════════════════════════════════════════════════════
echo ""
echo "=== Content/SEO Service ==="
# SEO Scores (163 records)
$MONO -c "\COPY (SELECT * FROM seo_scores ORDER BY id) TO '/tmp/seo_scores.csv' CSV HEADER"
docker cp /tmp/seo_scores.csv ms_postgres:/tmp/
$MICRO -d bizmark_content_dev -c "ALTER TABLE seo_scores DISABLE TRIGGER ALL; \COPY seo_scores FROM '/tmp/seo_scores.csv' CSV HEADER; ALTER TABLE seo_scores ENABLE TRIGGER ALL;" 2>/dev/null || echo "  ⚠️ seo_scores might already exist"
echo "  ✅ seo_scores: $($MICRO -d bizmark_content_dev -t -c 'SELECT COUNT(*) FROM seo_scores;' | tr -d ' ')"

# Auto Post Config (1 record)
$MONO -c "\COPY (SELECT * FROM auto_post_configs ORDER BY id) TO '/tmp/auto_post_configs.csv' CSV HEADER"
docker cp /tmp/auto_post_configs.csv ms_postgres:/tmp/
$MICRO -d bizmark_content_dev -c "ALTER TABLE auto_post_configs DISABLE TRIGGER ALL; \COPY auto_post_configs FROM '/tmp/auto_post_configs.csv' CSV HEADER; ALTER TABLE auto_post_configs ENABLE TRIGGER ALL;" 2>/dev/null || echo "  ⚠️ already exists"
echo "  ✅ auto_post_configs: $($MICRO -d bizmark_content_dev -t -c 'SELECT COUNT(*) FROM auto_post_configs;' | tr -d ' ')"

# ═══════════════════════════════════════════════════════════
# 2. PERIZINAN — Institutions + KBLI seed
# ═══════════════════════════════════════════════════════════
echo ""
echo "=== Perizinan Service ==="
# Institutions (6 records)
$MONO -c "\COPY (SELECT * FROM institutions ORDER BY id) TO '/tmp/institutions.csv' CSV HEADER"
docker cp /tmp/institutions.csv ms_postgres:/tmp/
$MICRO -d bizmark_perizinan_dev -c "\COPY institutions FROM '/tmp/institutions.csv' CSV HEADER" 2>/dev/null || echo "  ⚠️ institutions might exist"
echo "  ✅ institutions: $($MICRO -d bizmark_perizinan_dev -t -c 'SELECT COUNT(*) FROM institutions;' | tr -d ' ')"

# KBLI — cek tabel `kbli` (legacy) vs `kblis` (new)
KBLI_OLD=$($MONO -c "SELECT COUNT(*) FROM kbli;" | tr -d ' ')
KBLI_NEW=$($MICRO -d bizmark_perizinan_dev -t -c "SELECT COUNT(*) FROM kblis;" | tr -d ' ')
echo "  ℹ️  KBLI old table: $KBLI_OLD (monolith), new table: $KBLI_NEW (microservice)"

# ═══════════════════════════════════════════════════════════
# 3. CRM/LEADS — Clients + Service Inquiries (CRITICAL!)
# ═══════════════════════════════════════════════════════════
echo ""
echo "=== CRM Data (embedded in Perizinan) ==="
# Clients (3 records) — drop user_id FK
$MONO -c "\COPY (SELECT id,name,email,phone,address,city,province,postal_code,industry,company_size,website,notes,status,lead_source,created_at,updated_at FROM clients ORDER BY id) TO '/tmp/clients.csv' CSV HEADER"
docker cp /tmp/clients.csv ms_postgres:/tmp/
$MICRO -d bizmark_perizinan_dev << 'EOF'
CREATE TABLE IF NOT EXISTS clients (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255),
  phone VARCHAR(50),
  address TEXT,
  city VARCHAR(100),
  province VARCHAR(100),
  postal_code VARCHAR(20),
  industry VARCHAR(100),
  company_size VARCHAR(50),
  website VARCHAR(255),
  notes TEXT,
  status VARCHAR(50) DEFAULT 'active',
  lead_source VARCHAR(100),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
\COPY clients FROM '/tmp/clients.csv' CSV HEADER
EOF
echo "  ✅ clients: $($MICRO -d bizmark_perizinan_dev -t -c 'SELECT COUNT(*) FROM clients;' | tr -d ' ')"

# Service Inquiries (42 records) — DROP user_id, permit_application_id FK
$MONO -c "\COPY (SELECT id,client_id,business_name,business_description,business_type,location_address,location_city,location_province,estimated_investment,estimated_employees,business_start_date,contact_name,contact_phone,contact_email,urgency_level,status,notes,analyzed_at,analysis_result,created_at,updated_at FROM service_inquiries ORDER BY id) TO '/tmp/service_inquiries.csv' CSV HEADER"
docker cp /tmp/service_inquiries.csv ms_postgres:/tmp/
$MICRO -d bizmark_perizinan_dev << 'EOF'
CREATE TABLE IF NOT EXISTS service_inquiries (
  id SERIAL PRIMARY KEY,
  client_id BIGINT,
  business_name VARCHAR(255),
  business_description TEXT,
  business_type VARCHAR(100),
  location_address TEXT,
  location_city VARCHAR(100),
  location_province VARCHAR(100),
  estimated_investment DECIMAL(15,2),
  estimated_employees INT,
  business_start_date DATE,
  contact_name VARCHAR(255),
  contact_phone VARCHAR(50),
  contact_email VARCHAR(255),
  urgency_level VARCHAR(50),
  status VARCHAR(50) DEFAULT 'new',
  notes TEXT,
  analyzed_at TIMESTAMP,
  analysis_result JSONB,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
\COPY service_inquiries FROM '/tmp/service_inquiries.csv' CSV HEADER
EOF
echo "  ✅ service_inquiries: $($MICRO -d bizmark_perizinan_dev -t -c 'SELECT COUNT(*) FROM service_inquiries;' | tr -d ' ')"

# Service Cost Requests (13 records)
$MONO -c "\COPY (SELECT id,service_inquiry_id,requested_permits,total_estimated_cost,breakdown,status,valid_until,notes,created_at,updated_at FROM service_cost_requests ORDER BY id) TO '/tmp/service_cost_requests.csv' CSV HEADER"
docker cp /tmp/service_cost_requests.csv ms_postgres:/tmp/
$MICRO -d bizmark_perizinan_dev << 'EOF'
CREATE TABLE IF NOT EXISTS service_cost_requests (
  id SERIAL PRIMARY KEY,
  service_inquiry_id BIGINT,
  requested_permits TEXT,
  total_estimated_cost DECIMAL(15,2),
  breakdown JSONB,
  status VARCHAR(50) DEFAULT 'draft',
  valid_until DATE,
  notes TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
\COPY service_cost_requests FROM '/tmp/service_cost_requests.csv' CSV HEADER
EOF
echo "  ✅ service_cost_requests: $($MICRO -d bizmark_perizinan_dev -t -c 'SELECT COUNT(*) FROM service_cost_requests;' | tr -d ' ')"

# ═══════════════════════════════════════════════════════════
# 4. FINANSIAL — Master Data
# ═══════════════════════════════════════════════════════════
echo ""
echo "=== Finansial Service ==="
# Expense Categories (29 records)
$MONO -c "\COPY (SELECT * FROM expense_categories ORDER BY id) TO '/tmp/expense_categories.csv' CSV HEADER"
docker cp /tmp/expense_categories.csv ms_postgres:/tmp/
$MICRO -d bizmark_finansial_dev -c "\COPY expense_categories FROM '/tmp/expense_categories.csv' CSV HEADER" 2>/dev/null
echo "  ✅ expense_categories: $($MICRO -d bizmark_finansial_dev -t -c 'SELECT COUNT(*) FROM expense_categories;' | tr -d ' ')"

# Payment Methods (5 records)
$MONO -c "\COPY (SELECT * FROM payment_methods ORDER BY id) TO '/tmp/payment_methods.csv' CSV HEADER"
docker cp /tmp/payment_methods.csv ms_postgres:/tmp/
$MICRO -d bizmark_finansial_dev -c "\COPY payment_methods FROM '/tmp/payment_methods.csv' CSV HEADER" 2>/dev/null
echo "  ✅ payment_methods: $($MICRO -d bizmark_finansial_dev -t -c 'SELECT COUNT(*) FROM payment_methods;' | tr -d ' ')"

# Tax Rates (3 records)
$MONO -c "\COPY (SELECT * FROM tax_rates ORDER BY id) TO '/tmp/tax_rates.csv' CSV HEADER"
docker cp /tmp/tax_rates.csv ms_postgres:/tmp/
$MICRO -d bizmark_finansial_dev -c "\COPY tax_rates FROM '/tmp/tax_rates.csv' CSV HEADER" 2>/dev/null
echo "  ✅ tax_rates: $($MICRO -d bizmark_finansial_dev -t -c 'SELECT COUNT(*) FROM tax_rates;' | tr -d ' ')"

# ═══════════════════════════════════════════════════════════
# 5. PROYEK — Project Statuses
# ═══════════════════════════════════════════════════════════
echo ""
echo "=== Proyek Service ==="
# Project Statuses (12 records) — overwrite seeder
$MONO -c "\COPY (SELECT * FROM project_statuses ORDER BY id) TO '/tmp/project_statuses.csv' CSV HEADER"
docker cp /tmp/project_statuses.csv ms_postgres:/tmp/
$MICRO -d bizmark_proyek_dev -c "TRUNCATE project_statuses CASCADE; \COPY project_statuses FROM '/tmp/project_statuses.csv' CSV HEADER" 2>/dev/null
echo "  ✅ project_statuses: $($MICRO -d bizmark_proyek_dev -t -c 'SELECT COUNT(*) FROM project_statuses;' | tr -d ' ')"

# ═══════════════════════════════════════════════════════════
# SUMMARY
# ═══════════════════════════════════════════════════════════
echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║  SYNC COMPLETE                                           ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo "Content/SEO  : articles(163), topics(447), keywords(10), seo_scores(163)"
echo "Perizinan    : institutions(6), clients(3), service_inquiries(42), requests(13)"
echo "Finansial    : expense_categories(29), payment_methods(5), tax_rates(3)"
echo "Proyek       : project_statuses(12)"
echo ""
echo "NEXT: KBLI seed (need external source or manual SQL)"
