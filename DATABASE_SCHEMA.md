# Database Schema Design - Platform SaaS Perizinan UMKM

## Overview
Database menggunakan PostgreSQL dengan Row-Level Security (RLS) untuk multi-tenancy. Setiap tenant (UMKM) memiliki isolasi data yang kuat melalui RLS policies.

## Database Configuration

### PostgreSQL Settings untuk VPS 8GB RAM
```sql
-- postgresql.conf optimizations
shared_buffers = 2GB                    -- 25% of RAM
work_mem = 64MB                         -- Per operation
effective_cache_size = 6GB              -- 75% of RAM
wal_buffers = 16MB
max_connections = 100
random_page_cost = 1.1                  -- For SSD
effective_io_concurrency = 200          -- For SSD
```

### Extensions Required
```sql
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pgcrypto";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";
CREATE EXTENSION IF NOT EXISTS "btree_gin";
```

## Core Schema

### Tenants Table
```sql
CREATE TABLE tenants (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    settings JSONB DEFAULT '{}',
    subscription_plan VARCHAR(50) DEFAULT 'basic',
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_tenants_slug ON tenants(slug);
CREATE INDEX idx_tenants_active ON tenants(is_active);
```

### Users Table
```sql
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role user_role NOT NULL DEFAULT 'client',
    is_active BOOLEAN DEFAULT true,
    email_verified_at TIMESTAMP WITH TIME ZONE,
    last_login_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    
    CONSTRAINT unique_email_per_tenant UNIQUE(tenant_id, email)
);

-- User roles enum
CREATE TYPE user_role AS ENUM ('admin', 'client', 'consultant');

-- Indexes
CREATE INDEX idx_users_tenant_id ON users(tenant_id);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- Row Level Security
ALTER TABLE users ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON users
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

### User Profiles Table
```sql
CREATE TABLE user_profiles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    company_name VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    npwp VARCHAR(20),
    avatar_url VARCHAR(500),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE UNIQUE INDEX idx_user_profiles_user_id ON user_profiles(user_id);
CREATE INDEX idx_user_profiles_tenant_id ON user_profiles(tenant_id);

-- Row Level Security
ALTER TABLE user_profiles ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON user_profiles
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

## License Management Schema

### License Types Table
```sql
CREATE TABLE license_types (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) NOT NULL,
    required_documents TEXT[] DEFAULT '{}',
    form_schema JSONB NOT NULL DEFAULT '{}',
    processing_time_days INTEGER DEFAULT 14,
    fee DECIMAL(15,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_by UUID NOT NULL REFERENCES users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_license_types_tenant_id ON license_types(tenant_id);
CREATE INDEX idx_license_types_category ON license_types(category);
CREATE INDEX idx_license_types_active ON license_types(is_active);

-- Row Level Security
ALTER TABLE license_types ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON license_types
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

### License Applications Table
```sql
CREATE TABLE license_applications (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    license_type_id UUID NOT NULL REFERENCES license_types(id),
    applicant_id UUID NOT NULL REFERENCES users(id),
    application_data JSONB NOT NULL DEFAULT '{}',
    status application_status DEFAULT 'draft',
    submitted_at TIMESTAMP WITH TIME ZONE,
    processed_at TIMESTAMP WITH TIME ZONE,
    processed_by UUID REFERENCES users(id),
    notes TEXT,
    reference_number VARCHAR(100) UNIQUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Application status enum
CREATE TYPE application_status AS ENUM (
    'draft', 'submitted', 'under_review', 'additional_info_required', 
    'approved', 'rejected', 'cancelled'
);

-- Generate reference number function
CREATE OR REPLACE FUNCTION generate_reference_number()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.reference_number IS NULL AND NEW.status = 'submitted' THEN
        NEW.reference_number := 'APP-' || 
            EXTRACT(YEAR FROM NOW()) || '-' ||
            LPAD(EXTRACT(MONTH FROM NOW())::TEXT, 2, '0') || '-' ||
            LPAD(nextval('reference_number_seq')::TEXT, 6, '0');
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE SEQUENCE reference_number_seq;

CREATE TRIGGER set_reference_number
    BEFORE INSERT OR UPDATE ON license_applications
    FOR EACH ROW EXECUTE FUNCTION generate_reference_number();

-- Indexes
CREATE INDEX idx_license_applications_tenant_id ON license_applications(tenant_id);
CREATE INDEX idx_license_applications_applicant ON license_applications(applicant_id);
CREATE INDEX idx_license_applications_status ON license_applications(status);
CREATE INDEX idx_license_applications_type ON license_applications(license_type_id);
CREATE INDEX idx_license_applications_submitted ON license_applications(submitted_at);

-- Row Level Security
ALTER TABLE license_applications ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON license_applications
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

### Application History Table
```sql
CREATE TABLE application_history (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    application_id UUID NOT NULL REFERENCES license_applications(id) ON DELETE CASCADE,
    previous_status application_status,
    new_status application_status NOT NULL,
    notes TEXT,
    changed_by UUID NOT NULL REFERENCES users(id),
    changed_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_application_history_tenant_id ON application_history(tenant_id);
CREATE INDEX idx_application_history_application ON application_history(application_id);
CREATE INDEX idx_application_history_changed_at ON application_history(changed_at);

-- Row Level Security
ALTER TABLE application_history ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON application_history
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);

-- Trigger to log status changes
CREATE OR REPLACE FUNCTION log_application_status_change()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.status IS DISTINCT FROM NEW.status THEN
        INSERT INTO application_history (
            tenant_id, application_id, previous_status, new_status, 
            notes, changed_by
        ) VALUES (
            NEW.tenant_id, NEW.id, OLD.status, NEW.status, 
            NEW.notes, NEW.processed_by
        );
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER application_status_change_log
    AFTER UPDATE ON license_applications
    FOR EACH ROW EXECUTE FUNCTION log_application_status_change();
```

## Document Management Schema

### Documents Table
```sql
CREATE TABLE documents (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    uploaded_by UUID NOT NULL REFERENCES users(id),
    file_name VARCHAR(500) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size BIGINT NOT NULL,
    file_path VARCHAR(1000) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    is_public BOOLEAN DEFAULT false,
    checksum VARCHAR(64),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_documents_tenant_id ON documents(tenant_id);
CREATE INDEX idx_documents_uploaded_by ON documents(uploaded_by);
CREATE INDEX idx_documents_category ON documents(category);
CREATE INDEX idx_documents_created_at ON documents(created_at);
CREATE INDEX idx_documents_checksum ON documents(checksum);

-- Row Level Security
ALTER TABLE documents ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON documents
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

### Application Documents Table (Junction)
```sql
CREATE TABLE application_documents (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    application_id UUID NOT NULL REFERENCES license_applications(id) ON DELETE CASCADE,
    document_id UUID NOT NULL REFERENCES documents(id) ON DELETE CASCADE,
    document_type VARCHAR(100) NOT NULL,
    is_required BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    
    CONSTRAINT unique_app_doc_type UNIQUE(application_id, document_type)
);

-- Indexes
CREATE INDEX idx_application_documents_tenant_id ON application_documents(tenant_id);
CREATE INDEX idx_application_documents_application ON application_documents(application_id);
CREATE INDEX idx_application_documents_document ON application_documents(document_id);

-- Row Level Security
ALTER TABLE application_documents ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON application_documents
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

## Financial & Reporting Schema

### Financial Transactions Table
```sql
CREATE TABLE financial_transactions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    transaction_type transaction_type NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT NOT NULL,
    reference_id UUID, -- Could reference license_applications or other entities
    reference_type VARCHAR(50),
    transaction_date DATE NOT NULL,
    recorded_by UUID NOT NULL REFERENCES users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Transaction types enum
CREATE TYPE transaction_type AS ENUM ('income', 'expense');

-- Indexes
CREATE INDEX idx_financial_transactions_tenant_id ON financial_transactions(tenant_id);
CREATE INDEX idx_financial_transactions_type ON financial_transactions(transaction_type);
CREATE INDEX idx_financial_transactions_category ON financial_transactions(category);
CREATE INDEX idx_financial_transactions_date ON financial_transactions(transaction_date);
CREATE INDEX idx_financial_transactions_reference ON financial_transactions(reference_id, reference_type);

-- Row Level Security
ALTER TABLE financial_transactions ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON financial_transactions
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

### Tax Records Table
```sql
CREATE TABLE tax_records (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    tax_type VARCHAR(50) NOT NULL,
    tax_period_year INTEGER NOT NULL,
    tax_period_month INTEGER, -- NULL for annual records
    taxable_income DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    payment_date DATE,
    status tax_status DEFAULT 'unpaid',
    notes TEXT,
    created_by UUID NOT NULL REFERENCES users(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    
    CONSTRAINT unique_tax_period UNIQUE(tenant_id, tax_type, tax_period_year, tax_period_month)
);

-- Tax status enum
CREATE TYPE tax_status AS ENUM ('unpaid', 'partially_paid', 'paid', 'overdue');

-- Indexes
CREATE INDEX idx_tax_records_tenant_id ON tax_records(tenant_id);
CREATE INDEX idx_tax_records_type ON tax_records(tax_type);
CREATE INDEX idx_tax_records_period ON tax_records(tax_period_year, tax_period_month);
CREATE INDEX idx_tax_records_status ON tax_records(status);

-- Row Level Security
ALTER TABLE tax_records ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON tax_records
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

### Reports Table
```sql
CREATE TABLE reports (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    report_type VARCHAR(100) NOT NULL,
    report_subtype VARCHAR(100),
    title VARCHAR(255) NOT NULL,
    parameters JSONB DEFAULT '{}',
    file_path VARCHAR(1000),
    file_size BIGINT,
    status report_status DEFAULT 'generating',
    error_message TEXT,
    generated_by UUID NOT NULL REFERENCES users(id),
    generated_at TIMESTAMP WITH TIME ZONE,
    expires_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Report status enum
CREATE TYPE report_status AS ENUM ('generating', 'completed', 'failed', 'expired');

-- Indexes
CREATE INDEX idx_reports_tenant_id ON reports(tenant_id);
CREATE INDEX idx_reports_type ON reports(report_type, report_subtype);
CREATE INDEX idx_reports_status ON reports(status);
CREATE INDEX idx_reports_generated_by ON reports(generated_by);
CREATE INDEX idx_reports_expires_at ON reports(expires_at);

-- Row Level Security
ALTER TABLE reports ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON reports
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

## Notification System Schema

### Notifications Table
```sql
CREATE TABLE notifications (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(100) NOT NULL,
    data JSONB DEFAULT '{}',
    is_read BOOLEAN DEFAULT false,
    read_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_notifications_tenant_id ON notifications(tenant_id);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_notifications_type ON notifications(notification_type);
CREATE INDEX idx_notifications_read ON notifications(is_read);
CREATE INDEX idx_notifications_created_at ON notifications(created_at);

-- Row Level Security
ALTER TABLE notifications ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON notifications
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

### Notification Templates Table
```sql
CREATE TABLE notification_templates (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID REFERENCES tenants(id) ON DELETE CASCADE, -- NULL for global templates
    template_key VARCHAR(100) NOT NULL,
    title_template VARCHAR(255) NOT NULL,
    message_template TEXT NOT NULL,
    email_subject_template VARCHAR(255),
    email_body_template TEXT,
    sms_template VARCHAR(500),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    
    CONSTRAINT unique_template_key UNIQUE(tenant_id, template_key)
);

-- Indexes
CREATE INDEX idx_notification_templates_tenant_id ON notification_templates(tenant_id);
CREATE INDEX idx_notification_templates_key ON notification_templates(template_key);
CREATE INDEX idx_notification_templates_active ON notification_templates(is_active);

-- Row Level Security
ALTER TABLE notification_templates ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON notification_templates
    FOR ALL TO authenticated_user
    USING (tenant_id IS NULL OR tenant_id = current_setting('app.current_tenant')::uuid);
```

## Job Queue Schema

### Background Jobs Table
```sql
CREATE TABLE background_jobs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID REFERENCES tenants(id) ON DELETE CASCADE,
    job_type VARCHAR(100) NOT NULL,
    job_data JSONB DEFAULT '{}',
    status job_status DEFAULT 'pending',
    priority INTEGER DEFAULT 0,
    attempts INTEGER DEFAULT 0,
    max_attempts INTEGER DEFAULT 3,
    error_message TEXT,
    scheduled_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    started_at TIMESTAMP WITH TIME ZONE,
    completed_at TIMESTAMP WITH TIME ZONE,
    failed_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Job status enum
CREATE TYPE job_status AS ENUM ('pending', 'processing', 'completed', 'failed', 'cancelled');

-- Indexes
CREATE INDEX idx_background_jobs_tenant_id ON background_jobs(tenant_id);
CREATE INDEX idx_background_jobs_type ON background_jobs(job_type);
CREATE INDEX idx_background_jobs_status ON background_jobs(status);
CREATE INDEX idx_background_jobs_scheduled ON background_jobs(scheduled_at);
CREATE INDEX idx_background_jobs_priority ON background_jobs(priority);

-- Row Level Security
ALTER TABLE background_jobs ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON background_jobs
    FOR ALL TO authenticated_user
    USING (tenant_id IS NULL OR tenant_id = current_setting('app.current_tenant')::uuid);
```

## Session Management Schema

### User Sessions Table
```sql
CREATE TABLE user_sessions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    tenant_id UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    refresh_token_hash VARCHAR(255) NOT NULL,
    device_info JSONB DEFAULT '{}',
    ip_address INET,
    user_agent TEXT,
    is_active BOOLEAN DEFAULT true,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_user_sessions_user_id ON user_sessions(user_id);
CREATE INDEX idx_user_sessions_tenant_id ON user_sessions(tenant_id);
CREATE INDEX idx_user_sessions_token_hash ON user_sessions(refresh_token_hash);
CREATE INDEX idx_user_sessions_expires_at ON user_sessions(expires_at);
CREATE INDEX idx_user_sessions_active ON user_sessions(is_active);

-- Row Level Security
ALTER TABLE user_sessions ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON user_sessions
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

## Audit Trail Schema

### Audit Logs Table
```sql
CREATE TABLE audit_logs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    tenant_id UUID REFERENCES tenants(id) ON DELETE CASCADE,
    user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id UUID NOT NULL,
    old_values JSONB,
    new_values JSONB,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_audit_logs_tenant_id ON audit_logs(tenant_id);
CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_action ON audit_logs(action);
CREATE INDEX idx_audit_logs_entity ON audit_logs(entity_type, entity_id);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at);

-- Row Level Security
ALTER TABLE audit_logs ENABLE ROW LEVEL SECURITY;

CREATE POLICY tenant_isolation ON audit_logs
    FOR ALL TO authenticated_user
    USING (tenant_id IS NULL OR tenant_id = current_setting('app.current_tenant')::uuid);
```

## Database Functions & Procedures

### Set Tenant Context Function
```sql
CREATE OR REPLACE FUNCTION set_tenant_context(tenant_uuid UUID)
RETURNS void AS $$
BEGIN
    PERFORM set_config('app.current_tenant', tenant_uuid::TEXT, true);
END;
$$ LANGUAGE plpgsql;
```

### Get Current Tenant Function
```sql
CREATE OR REPLACE FUNCTION get_current_tenant()
RETURNS UUID AS $$
BEGIN
    RETURN current_setting('app.current_tenant', true)::UUID;
EXCEPTION
    WHEN OTHERS THEN
        RETURN NULL;
END;
$$ LANGUAGE plpgsql;
```

### Clean Expired Sessions Procedure
```sql
CREATE OR REPLACE FUNCTION clean_expired_sessions()
RETURNS void AS $$
BEGIN
    DELETE FROM user_sessions 
    WHERE expires_at < NOW() OR is_active = false;
    
    DELETE FROM reports 
    WHERE status = 'expired' OR (expires_at < NOW() AND expires_at IS NOT NULL);
END;
$$ LANGUAGE plpgsql;

-- Schedule cleanup job (requires pg_cron extension)
-- SELECT cron.schedule('clean-expired-sessions', '0 2 * * *', 'SELECT clean_expired_sessions();');
```

### Statistics Functions
```sql
CREATE OR REPLACE FUNCTION get_tenant_stats(tenant_uuid UUID)
RETURNS JSONB AS $$
DECLARE
    result JSONB;
BEGIN
    PERFORM set_tenant_context(tenant_uuid);
    
    WITH stats AS (
        SELECT 
            (SELECT COUNT(*) FROM license_applications WHERE tenant_id = tenant_uuid) as total_applications,
            (SELECT COUNT(*) FROM license_applications WHERE tenant_id = tenant_uuid AND status = 'pending') as pending_applications,
            (SELECT COUNT(*) FROM license_applications WHERE tenant_id = tenant_uuid AND status = 'approved') as approved_applications,
            (SELECT COUNT(*) FROM documents WHERE tenant_id = tenant_uuid) as total_documents,
            (SELECT COUNT(*) FROM users WHERE tenant_id = tenant_uuid AND is_active = true) as active_users
    )
    SELECT jsonb_build_object(
        'applications', jsonb_build_object(
            'total', total_applications,
            'pending', pending_applications,
            'approved', approved_applications
        ),
        'documents', total_documents,
        'users', active_users
    ) INTO result FROM stats;
    
    RETURN result;
END;
$$ LANGUAGE plpgsql;
```

## Indexes for Performance

### Composite Indexes for Common Queries
```sql
-- License applications with status and date filtering
CREATE INDEX idx_license_apps_tenant_status_date 
ON license_applications(tenant_id, status, submitted_at DESC);

-- Documents with category and upload date
CREATE INDEX idx_documents_tenant_category_date 
ON documents(tenant_id, category, created_at DESC);

-- Notifications for user dashboard
CREATE INDEX idx_notifications_user_read_date 
ON notifications(user_id, is_read, created_at DESC);

-- Financial transactions by date range
CREATE INDEX idx_financial_trans_tenant_date_type 
ON financial_transactions(tenant_id, transaction_date, transaction_type);

-- Full-text search indexes
CREATE INDEX idx_license_types_search 
ON license_types USING gin(to_tsvector('indonesian', name || ' ' || description));

CREATE INDEX idx_applications_search 
ON license_applications USING gin(to_tsvector('indonesian', application_data::text));
```

## Database Maintenance

### Regular Maintenance Tasks
```sql
-- Update table statistics
ANALYZE;

-- Reindex for optimal performance (run during low usage)
REINDEX DATABASE bizmark_db;

-- Check for bloated tables
SELECT 
    tablename,
    pg_size_pretty(pg_total_relation_size(tablename::regclass)) as size,
    pg_stat_get_live_tuples(tablename::regclass) as live_tuples,
    pg_stat_get_dead_tuples(tablename::regclass) as dead_tuples
FROM pg_tables 
WHERE schemaname = 'public';

-- Vacuum bloated tables
VACUUM (ANALYZE) license_applications;
VACUUM (ANALYZE) documents;
VACUUM (ANALYZE) audit_logs;
```

### Backup Strategy
```bash
# Daily full backup
pg_dump -h localhost -U postgres -d bizmark_db -f /backup/bizmark_$(date +%Y%m%d).sql

# Weekly compressed backup
pg_dump -h localhost -U postgres -d bizmark_db | gzip > /backup/bizmark_weekly_$(date +%Y%m%d).sql.gz

# Point-in-time recovery setup
# Configure WAL archiving in postgresql.conf:
# wal_level = replica
# archive_mode = on
# archive_command = 'cp %p /backup/wal/%f'
```

Dokumen ini memberikan blueprint lengkap untuk struktur database yang mendukung platform SaaS perizinan UMKM dengan multi-tenancy, keamanan data, dan performa yang optimal pada VPS terbatas.
