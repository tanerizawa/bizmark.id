-- ═══════════════════════════════════════════════════════════
-- FASE 1: DATA IMPORT - Production → Microservices
-- Execute: PGPASSWORD='T@n12089' psql -h 127.0.0.1 -U hadez -d bizmark_db -f import_all_production_data.sql
-- ═══════════════════════════════════════════════════════════

-- 1.1 KBLI Export (1793 records)
\echo '=== Exporting KBLI (1793 records) ==='
\COPY (SELECT code, description, description, category, sector, COALESCE(complexity_level, 'medium'), 'medium', created_at, updated_at FROM kbli ORDER BY code) TO '/tmp/kbli_1793.csv' CSV HEADER

-- 1.2 Clients Export (3 records)
\echo '=== Exporting Clients (3 records) ==='
\COPY (SELECT id, name, COALESCE(company_name, name), email, phone, address, city, province, industry, contact_person, status, notes, created_at, updated_at FROM clients ORDER BY id) TO '/tmp/clients_3.csv' CSV HEADER

-- 1.3 Service Inquiries Export (42 records)
\echo '=== Exporting Service Inquiries (42 leads) ==='
\COPY (SELECT id, client_id, business_name, business_description, business_type, location_address, location_city, location_province, estimated_investment, estimated_employees, business_start_date, contact_name, contact_phone, contact_email, urgency_level, status, notes, analyzed_at, analysis_result, created_at, updated_at FROM service_inquiries ORDER BY id) TO '/tmp/service_inquiries_42.csv' CSV HEADER

-- 1.4 Service Cost Requests Export (13 records)
\echo '=== Exporting Cost Requests (13 records) ==='
\COPY (SELECT id, service_inquiry_id, requested_permits, total_estimated_cost, breakdown, status, valid_until, notes, created_at, updated_at FROM service_cost_requests ORDER BY id) TO '/tmp/cost_requests_13.csv' CSV HEADER

-- 1.5 Finansial Master Data Export
\echo '=== Exporting Expense Categories (29) ==='
\COPY (SELECT * FROM expense_categories ORDER BY id) TO '/tmp/expense_categories.csv' CSV HEADER

\echo '=== Exporting Payment Methods (5) ==='
\COPY (SELECT * FROM payment_methods ORDER BY id) TO '/tmp/payment_methods.csv' CSV HEADER

\echo '=== Exporting Tax Rates (3) ==='
\COPY (SELECT * FROM tax_rates ORDER BY id) TO '/tmp/tax_rates.csv' CSV HEADER

\echo '✅ All exports complete. Files in /tmp/'
\echo 'Next: Copy to container and import'
