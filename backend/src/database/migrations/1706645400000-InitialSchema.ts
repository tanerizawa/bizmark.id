import { MigrationInterface, QueryRunner } from 'typeorm';

export class InitialSchema1706645400000 implements MigrationInterface {
  name = 'InitialSchema1706645400000';

  public async up(queryRunner: QueryRunner): Promise<void> {
    // Enable UUID extension
    await queryRunner.query(`CREATE EXTENSION IF NOT EXISTS "uuid-ossp"`);

    // Create tenants table
    await queryRunner.query(`
      CREATE TABLE "tenants" (
        "id" uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
        "name" character varying(255) NOT NULL,
        "slug" character varying(100) NOT NULL UNIQUE,
        "type" character varying(50) NOT NULL DEFAULT 'business',
        "status" character varying(50) NOT NULL DEFAULT 'active',
        "settings" jsonb,
        "created_at" TIMESTAMP NOT NULL DEFAULT now(),
        "updated_at" TIMESTAMP NOT NULL DEFAULT now()
      )
    `);

    // Create users table
    await queryRunner.query(`
      CREATE TABLE "users" (
        "id" uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
        "email" character varying(255) NOT NULL,
        "password" character varying(255) NOT NULL,
        "first_name" character varying(255) NOT NULL,
        "last_name" character varying(255) NOT NULL,
        "phone" character varying(50),
        "role" character varying(50) NOT NULL DEFAULT 'applicant',
        "status" character varying(50) NOT NULL DEFAULT 'active',
        "email_verified" boolean NOT NULL DEFAULT false,
        "email_verified_at" TIMESTAMP,
        "reset_token" character varying(255),
        "reset_token_expires_at" TIMESTAMP,
        "last_login_at" TIMESTAMP,
        "settings" jsonb,
        "tenant_id" uuid NOT NULL,
        "created_at" TIMESTAMP NOT NULL DEFAULT now(),
        "updated_at" TIMESTAMP NOT NULL DEFAULT now(),
        CONSTRAINT "FK_users_tenant" FOREIGN KEY ("tenant_id") REFERENCES "tenants"("id") ON DELETE CASCADE,
        CONSTRAINT "UQ_users_email_tenant" UNIQUE ("email", "tenant_id")
      )
    `);

    // Create license_types table
    await queryRunner.query(`
      CREATE TABLE "license_types" (
        "id" uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
        "name" character varying(255) NOT NULL,
        "code" character varying(50) NOT NULL,
        "description" text,
        "category" character varying(100),
        "required_documents" jsonb,
        "processing_time_days" integer DEFAULT 30,
        "fee_amount" decimal(12,2) DEFAULT 0,
        "status" character varying(50) NOT NULL DEFAULT 'active',
        "tenant_id" uuid NOT NULL,
        "created_at" TIMESTAMP NOT NULL DEFAULT now(),
        "updated_at" TIMESTAMP NOT NULL DEFAULT now(),
        CONSTRAINT "FK_license_types_tenant" FOREIGN KEY ("tenant_id") REFERENCES "tenants"("id") ON DELETE CASCADE,
        CONSTRAINT "UQ_license_types_code_tenant" UNIQUE ("code", "tenant_id")
      )
    `);

    // Create licenses table
    await queryRunner.query(`
      CREATE TABLE "licenses" (
        "id" uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
        "application_number" character varying(100) NOT NULL,
        "business_name" character varying(255) NOT NULL,
        "business_address" text NOT NULL,
        "business_phone" character varying(50),
        "business_email" character varying(255),
        "owner_name" character varying(255) NOT NULL,
        "owner_id_number" character varying(50) NOT NULL,
        "status" character varying(50) NOT NULL DEFAULT 'draft',
        "application_date" TIMESTAMP,
        "approval_date" TIMESTAMP,
        "expiry_date" TIMESTAMP,
        "valid_from" TIMESTAMP,
        "valid_until" TIMESTAMP,
        "reviewer_notes" text,
        "rejection_reason" text,
        "metadata" jsonb,
        "license_type_id" uuid NOT NULL,
        "applicant_id" uuid NOT NULL,
        "tenant_id" uuid NOT NULL,
        "created_at" TIMESTAMP NOT NULL DEFAULT now(),
        "updated_at" TIMESTAMP NOT NULL DEFAULT now(),
        CONSTRAINT "FK_licenses_license_type" FOREIGN KEY ("license_type_id") REFERENCES "license_types"("id") ON DELETE RESTRICT,
        CONSTRAINT "FK_licenses_applicant" FOREIGN KEY ("applicant_id") REFERENCES "users"("id") ON DELETE RESTRICT,
        CONSTRAINT "FK_licenses_tenant" FOREIGN KEY ("tenant_id") REFERENCES "tenants"("id") ON DELETE CASCADE,
        CONSTRAINT "UQ_licenses_application_number" UNIQUE ("application_number")
      )
    `);

    // Create documents table
    await queryRunner.query(`
      CREATE TABLE "documents" (
        "id" uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
        "filename" character varying(255) NOT NULL,
        "original_name" character varying(255) NOT NULL,
        "mime_type" character varying(100) NOT NULL,
        "size" integer NOT NULL,
        "path" character varying(500) NOT NULL,
        "type" character varying(50) NOT NULL,
        "status" character varying(50) NOT NULL DEFAULT 'active',
        "is_verified" boolean NOT NULL DEFAULT false,
        "verified_at" TIMESTAMP,
        "metadata" jsonb,
        "license_id" uuid,
        "uploaded_by" uuid NOT NULL,
        "tenant_id" uuid NOT NULL,
        "created_at" TIMESTAMP NOT NULL DEFAULT now(),
        "updated_at" TIMESTAMP NOT NULL DEFAULT now(),
        CONSTRAINT "FK_documents_license" FOREIGN KEY ("license_id") REFERENCES "licenses"("id") ON DELETE CASCADE,
        CONSTRAINT "FK_documents_uploaded_by" FOREIGN KEY ("uploaded_by") REFERENCES "users"("id") ON DELETE RESTRICT,
        CONSTRAINT "FK_documents_tenant" FOREIGN KEY ("tenant_id") REFERENCES "tenants"("id") ON DELETE CASCADE
      )
    `);

    // Create license_workflows table
    await queryRunner.query(`
      CREATE TABLE "license_workflows" (
        "id" uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
        "action" character varying(100) NOT NULL,
        "from_status" character varying(50),
        "to_status" character varying(50) NOT NULL,
        "notes" text,
        "metadata" jsonb,
        "license_id" uuid NOT NULL,
        "actor_id" uuid NOT NULL,
        "tenant_id" uuid NOT NULL,
        "created_at" TIMESTAMP NOT NULL DEFAULT now(),
        CONSTRAINT "FK_license_workflows_license" FOREIGN KEY ("license_id") REFERENCES "licenses"("id") ON DELETE CASCADE,
        CONSTRAINT "FK_license_workflows_actor" FOREIGN KEY ("actor_id") REFERENCES "users"("id") ON DELETE RESTRICT,
        CONSTRAINT "FK_license_workflows_tenant" FOREIGN KEY ("tenant_id") REFERENCES "tenants"("id") ON DELETE CASCADE
      )
    `);

    // Create notifications table
    await queryRunner.query(`
      CREATE TABLE "notifications" (
        "id" uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
        "type" character varying(50) NOT NULL DEFAULT 'in_app',
        "recipient" character varying(255) NOT NULL,
        "subject" character varying(500) NOT NULL,
        "content" text NOT NULL,
        "status" character varying(50) NOT NULL DEFAULT 'pending',
        "priority" character varying(50) NOT NULL DEFAULT 'normal',
        "metadata" jsonb,
        "scheduled_at" TIMESTAMP,
        "sent_at" TIMESTAMP,
        "delivered_at" TIMESTAMP,
        "read_at" TIMESTAMP,
        "failed_at" TIMESTAMP,
        "error_message" text,
        "retry_count" integer NOT NULL DEFAULT 0,
        "max_retries" integer NOT NULL DEFAULT 3,
        "user_id" uuid,
        "tenant_id" uuid NOT NULL,
        "created_at" TIMESTAMP NOT NULL DEFAULT now(),
        "updated_at" TIMESTAMP NOT NULL DEFAULT now(),
        CONSTRAINT "FK_notifications_user" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE SET NULL,
        CONSTRAINT "FK_notifications_tenant" FOREIGN KEY ("tenant_id") REFERENCES "tenants"("id") ON DELETE CASCADE
      )
    `);

    // Create audit_logs table
    await queryRunner.query(`
      CREATE TABLE "audit_logs" (
        "id" uuid PRIMARY KEY DEFAULT uuid_generate_v4(),
        "entity_type" character varying(100) NOT NULL,
        "entity_id" character varying(255) NOT NULL,
        "action" character varying(50) NOT NULL,
        "changes" jsonb,
        "ip_address" character varying(45),
        "user_agent" text,
        "user_id" uuid,
        "tenant_id" uuid NOT NULL,
        "created_at" TIMESTAMP NOT NULL DEFAULT now(),
        CONSTRAINT "FK_audit_logs_user" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE SET NULL,
        CONSTRAINT "FK_audit_logs_tenant" FOREIGN KEY ("tenant_id") REFERENCES "tenants"("id") ON DELETE CASCADE
      )
    `);

    // Create indexes for performance
    await queryRunner.query(`CREATE INDEX "IDX_users_tenant_id" ON "users" ("tenant_id")`);
    await queryRunner.query(`CREATE INDEX "IDX_users_email" ON "users" ("email")`);
    await queryRunner.query(`CREATE INDEX "IDX_users_role" ON "users" ("role")`);
    await queryRunner.query(`CREATE INDEX "IDX_users_status" ON "users" ("status")`);

    await queryRunner.query(`CREATE INDEX "IDX_licenses_tenant_id" ON "licenses" ("tenant_id")`);
    await queryRunner.query(
      `CREATE INDEX "IDX_licenses_applicant_id" ON "licenses" ("applicant_id")`,
    );
    await queryRunner.query(
      `CREATE INDEX "IDX_licenses_license_type_id" ON "licenses" ("license_type_id")`,
    );
    await queryRunner.query(`CREATE INDEX "IDX_licenses_status" ON "licenses" ("status")`);
    await queryRunner.query(
      `CREATE INDEX "IDX_licenses_application_date" ON "licenses" ("application_date")`,
    );

    await queryRunner.query(`CREATE INDEX "IDX_documents_tenant_id" ON "documents" ("tenant_id")`);
    await queryRunner.query(
      `CREATE INDEX "IDX_documents_license_id" ON "documents" ("license_id")`,
    );
    await queryRunner.query(
      `CREATE INDEX "IDX_documents_uploaded_by" ON "documents" ("uploaded_by")`,
    );
    await queryRunner.query(`CREATE INDEX "IDX_documents_type" ON "documents" ("type")`);

    await queryRunner.query(
      `CREATE INDEX "IDX_notifications_tenant_id" ON "notifications" ("tenant_id")`,
    );
    await queryRunner.query(
      `CREATE INDEX "IDX_notifications_user_id" ON "notifications" ("user_id")`,
    );
    await queryRunner.query(
      `CREATE INDEX "IDX_notifications_status" ON "notifications" ("status")`,
    );
    await queryRunner.query(`CREATE INDEX "IDX_notifications_type" ON "notifications" ("type")`);
    await queryRunner.query(
      `CREATE INDEX "IDX_notifications_created_at" ON "notifications" ("created_at")`,
    );

    await queryRunner.query(
      `CREATE INDEX "IDX_audit_logs_tenant_id" ON "audit_logs" ("tenant_id")`,
    );
    await queryRunner.query(`CREATE INDEX "IDX_audit_logs_user_id" ON "audit_logs" ("user_id")`);
    await queryRunner.query(
      `CREATE INDEX "IDX_audit_logs_entity" ON "audit_logs" ("entity_type", "entity_id")`,
    );
    await queryRunner.query(
      `CREATE INDEX "IDX_audit_logs_created_at" ON "audit_logs" ("created_at")`,
    );

    // Enable Row-Level Security (RLS)
    await queryRunner.query(`ALTER TABLE "users" ENABLE ROW LEVEL SECURITY`);
    await queryRunner.query(`ALTER TABLE "licenses" ENABLE ROW LEVEL SECURITY`);
    await queryRunner.query(`ALTER TABLE "license_types" ENABLE ROW LEVEL SECURITY`);
    await queryRunner.query(`ALTER TABLE "documents" ENABLE ROW LEVEL SECURITY`);
    await queryRunner.query(`ALTER TABLE "notifications" ENABLE ROW LEVEL SECURITY`);
    await queryRunner.query(`ALTER TABLE "audit_logs" ENABLE ROW LEVEL SECURITY`);
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    // Drop tables in reverse order
    await queryRunner.query(`DROP TABLE "audit_logs"`);
    await queryRunner.query(`DROP TABLE "notifications"`);
    await queryRunner.query(`DROP TABLE "license_workflows"`);
    await queryRunner.query(`DROP TABLE "documents"`);
    await queryRunner.query(`DROP TABLE "licenses"`);
    await queryRunner.query(`DROP TABLE "license_types"`);
    await queryRunner.query(`DROP TABLE "users"`);
    await queryRunner.query(`DROP TABLE "tenants"`);

    // Drop extension
    await queryRunner.query(`DROP EXTENSION IF EXISTS "uuid-ossp"`);
  }
}
