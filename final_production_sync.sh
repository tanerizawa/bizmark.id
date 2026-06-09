#!/bin/bash
set -e
export PGPASSWORD='T@n12089'
M="psql -h 127.0.0.1 -U hadez -d bizmark_db"
D="docker exec ms_postgres psql -U bizmark_ms"

echo "╔══════════════════════════════════════════════════════════╗"
echo "║          FINAL PRODUCTION DATA SYNC                      ║"
echo "╚══════════════════════════════════════════════════════════╝"

# KBLI 1793 records
echo "=== KBLI (1793 records) ==="
$M -c "\COPY (SELECT code,title,description,category,sector,complexity_level,risk_level,created_at,updated_at FROM kbli ORDER BY code) TO '/tmp/kbli_full.csv' CSV HEADER"
docker cp /tmp/kbli_full.csv ms_postgres:/tmp/
$D -d bizmark_perizinan_dev -c "ALTER TABLE kblis DROP CONSTRAINT IF EXISTS kblis_code_unique; TRUNCATE kblis; \COPY kblis(code,title,description,category,sector,complexity_level,risk_level,created_at,updated_at) FROM '/tmp/kbli_full.csv' CSV HEADER;"
echo "✅ $($D -d bizmark_perizinan_dev -t -c 'SELECT COUNT(*) FROM kblis;' | tr -d ' ') KBLI"

# Clients
echo "=== Clients (3) ==="
$M -c "\COPY (SELECT id,name,company_name,email,phone,address,city,province,industry,contact_person,status,notes,created_at,updated_at FROM clients ORDER BY id) TO '/tmp/clients.csv' CSV HEADER"
docker cp /tmp/clients.csv ms_postgres:/tmp/
$D -d bizmark_perizinan_dev << 'SQL'
DROP TABLE IF EXISTS clients CASCADE;
CREATE TABLE clients (id SERIAL PRIMARY KEY,name VARCHAR(255),company_name VARCHAR(255),email VARCHAR(255),phone VARCHAR(50),address TEXT,city VARCHAR(100),province VARCHAR(100),industry VARCHAR(100),contact_person VARCHAR(255),status VARCHAR(50) DEFAULT 'active',notes TEXT,created_at TIMESTAMP,updated_at TIMESTAMP);
\COPY clients FROM '/tmp/clients.csv' CSV HEADER
SQL
echo "✅ $($D -d bizmark_perizinan_dev -t -c 'SELECT COUNT(*) FROM clients;' | tr -d ' ') clients"

# Service Inquiries (42)
echo "=== Service Inquiries (42) ==="
$M -c "\COPY (SELECT id,client_id,business_name,business_description,business_type,location_address,location_city,location_province,estimated_investment,estimated_employees,business_start_date,contact_name,contact_phone,contact_email,urgency_level,status,notes,analyzed_at,analysis_result,created_at,updated_at FROM service_inquiries ORDER BY id) TO '/tmp/si.csv' CSV HEADER"
docker cp /tmp/si.csv ms_postgres:/tmp/
$D -d bizmark_perizinan_dev << 'SQL'
DROP TABLE IF EXISTS service_inquiries CASCADE;
CREATE TABLE service_inquiries (id SERIAL PRIMARY KEY,client_id BIGINT,business_name VARCHAR(255),business_description TEXT,business_type VARCHAR(100),location_address TEXT,location_city VARCHAR(100),location_province VARCHAR(100),estimated_investment DECIMAL(15,2),estimated_employees INT,business_start_date DATE,contact_name VARCHAR(255),contact_phone VARCHAR(50),contact_email VARCHAR(255),urgency_level VARCHAR(50),status VARCHAR(50) DEFAULT 'new',notes TEXT,analyzed_at TIMESTAMP,analysis_result JSONB,created_at TIMESTAMP,updated_at TIMESTAMP);
\COPY service_inquiries FROM '/tmp/si.csv' CSV HEADER
SQL
echo "✅ $($D -d bizmark_perizinan_dev -t -c 'SELECT COUNT(*) FROM service_inquiries;' | tr -d ' ') inquiries"

# Service Cost Requests (13)
echo "=== Cost Requests (13) ==="
$M -c "\COPY (SELECT id,service_inquiry_id,requested_permits,total_estimated_cost,breakdown,status,valid_until,notes,created_at,updated_at FROM service_cost_requests ORDER BY id) TO '/tmp/scr.csv' CSV HEADER"
docker cp /tmp/scr.csv ms_postgres:/tmp/
$D -d bizmark_perizinan_dev << 'SQL'
DROP TABLE IF EXISTS service_cost_requests CASCADE;
CREATE TABLE service_cost_requests (id SERIAL PRIMARY KEY,service_inquiry_id BIGINT,requested_permits TEXT,total_estimated_cost DECIMAL(15,2),breakdown JSONB,status VARCHAR(50),valid_until DATE,notes TEXT,created_at TIMESTAMP,updated_at TIMESTAMP);
\COPY service_cost_requests FROM '/tmp/scr.csv' CSV HEADER
SQL
echo "✅ $($D -d bizmark_perizinan_dev -t -c 'SELECT COUNT(*) FROM service_cost_requests;' | tr -d ' ') requests"

# Finansial master
echo "=== Finansial Master ==="
for tbl in expense_categories payment_methods tax_rates; do
  $M -c "\COPY (SELECT * FROM $tbl ORDER BY id) TO '/tmp/${tbl}.csv' CSV HEADER"
  docker cp /tmp/${tbl}.csv ms_postgres:/tmp/
  $D -d bizmark_finansial_dev -c "TRUNCATE $tbl CASCADE; \COPY $tbl FROM '/tmp/${tbl}.csv' CSV HEADER" 2>/dev/null
  echo "✅ $($D -d bizmark_finansial_dev -t -c "SELECT COUNT(*) FROM $tbl;" | tr -d ' ') $tbl"
done

# Proyek
echo "=== Proyek Statuses (12) ==="
$M -c "\COPY (SELECT * FROM project_statuses ORDER BY id) TO '/tmp/ps.csv' CSV HEADER"
docker cp /tmp/ps.csv ms_postgres:/tmp/
$D -d bizmark_proyek_dev -c "TRUNCATE project_statuses CASCADE; \COPY project_statuses FROM '/tmp/ps.csv' CSV HEADER"
echo "✅ $($D -d bizmark_proyek_dev -t -c 'SELECT COUNT(*) FROM project_statuses;' | tr -d ' ') statuses"

# Summary
echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║  SYNC COMPLETE — ALL PRODUCTION DATA MIGRATED            ║"
echo "╚══════════════════════════════════════════════════════════╝"
$D -d bizmark_content_dev -t -c "SELECT 'Content: '||COUNT(*)||' articles' FROM articles;"
$D -d bizmark_perizinan_dev -t -c "SELECT 'Perizinan: '||COUNT(*)||' KBLI, '||(SELECT COUNT(*) FROM service_inquiries)||' leads' FROM kblis;"
$D -d bizmark_finansial_dev -t -c "SELECT 'Finansial: '||COUNT(*)||' categories' FROM expense_categories;"
