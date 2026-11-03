-- =============================================================================
-- Complete PostgreSQL Database Schema
-- Project: Dental Chart Application (Laravel 11 + PostgreSQL)
-- Generated from: Complete Laravel Migration History (24 migration files)
-- Compatibility: PostgreSQL 12+
-- =============================================================================

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

BEGIN;

-- =============================================================================
-- SECTION 1: CORE AUTHENTICATION & ACCESS CONTROL TABLES
-- =============================================================================

-- Table: users
-- Core users table storing credentials, system flags, and timestamps.
-- Migrations:
--   - database/migrations/0001_01_01_000000_create_users_table.php
--   - Modules/User/database/migrations/2026_01_01_000005_add_username_to_users_table.php
--   - Modules/User/database/migrations/2026_01_01_000006_add_is_super_addmin_to_users_table.php
--   - Modules/Hospital/database/migrations/2026_01_01_000017_add_is_active_to_users_table.php
CREATE TABLE IF NOT EXISTS "users" (
    "id" BIGSERIAL PRIMARY KEY,
    "name" VARCHAR(255) NOT NULL,
    "username" VARCHAR(255) NULL,
    "email" VARCHAR(255) NOT NULL,
    "email_verified_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "password" VARCHAR(255) NOT NULL,
    "is_super_addmin" BOOLEAN NOT NULL DEFAULT FALSE,
    "is_active" BOOLEAN NOT NULL DEFAULT TRUE,
    "remember_token" VARCHAR(100) NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "users_email_unique" UNIQUE ("email"),
    CONSTRAINT "users_username_unique" UNIQUE ("username")
);

-- Table: password_reset_tokens
-- Stores password reset tokens mapped to email addresses.
-- Migration: database/migrations/0001_01_01_000000_create_users_table.php
CREATE TABLE IF NOT EXISTS "password_reset_tokens" (
    "email" VARCHAR(255) PRIMARY KEY,
    "token" VARCHAR(255) NOT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL
);

-- Table: sessions
-- Database session driver storage for authenticated sessions.
-- Migration: database/migrations/0001_01_01_000000_create_users_table.php
CREATE TABLE IF NOT EXISTS "sessions" (
    "id" VARCHAR(255) PRIMARY KEY,
    "user_id" BIGINT NULL,
    "ip_address" VARCHAR(45) NULL,
    "user_agent" TEXT NULL,
    "payload" TEXT NOT NULL,
    "last_activity" INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS "sessions_user_id_index" ON "sessions" ("user_id");
CREATE INDEX IF NOT EXISTS "sessions_last_activity_index" ON "sessions" ("last_activity");

-- Table: roles
-- Role definitions for RBAC authorization.
-- Migration: Modules/User/database/migrations/2026_01_01_000001_create_roles_table.php
CREATE TABLE IF NOT EXISTS "roles" (
    "id" BIGSERIAL PRIMARY KEY,
    "name" VARCHAR(255) NOT NULL,
    "label" VARCHAR(255) NOT NULL,
    "description" TEXT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "roles_name_unique" UNIQUE ("name")
);

-- Table: permissions
-- Granular permissions mapped across modules and controllers.
-- Migration: Modules/User/database/migrations/2026_01_01_000002_create_permissions_table.php
CREATE TABLE IF NOT EXISTS "permissions" (
    "id" BIGSERIAL PRIMARY KEY,
    "name" VARCHAR(255) NOT NULL,
    "module" VARCHAR(255) NOT NULL,
    "controller" VARCHAR(255) NOT NULL,
    "action" VARCHAR(255) NOT NULL,
    "label" VARCHAR(255) NULL,
    "description" TEXT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "permissions_name_unique" UNIQUE ("name")
);

CREATE INDEX IF NOT EXISTS "permissions_module_index" ON "permissions" ("module");
CREATE INDEX IF NOT EXISTS "permissions_controller_index" ON "permissions" ("controller");
CREATE INDEX IF NOT EXISTS "permissions_module_controller_index" ON "permissions" ("module", "controller");

-- Table: role_user
-- Many-to-many pivot relationship between roles and users.
-- Migration: Modules/User/database/migrations/2026_01_01_000003_create_role_user_table.php
CREATE TABLE IF NOT EXISTS "role_user" (
    "role_id" BIGINT NOT NULL,
    "user_id" BIGINT NOT NULL,
    PRIMARY KEY ("role_id", "user_id"),
    CONSTRAINT "role_user_role_id_foreign" FOREIGN KEY ("role_id") REFERENCES "roles" ("id") ON DELETE CASCADE,
    CONSTRAINT "role_user_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS "role_user_user_id_index" ON "role_user" ("user_id");

-- Table: permission_role
-- Many-to-many pivot relationship between permissions and roles.
-- Migration: Modules/User/database/migrations/2026_01_01_000004_create_permission_role_table.php
CREATE TABLE IF NOT EXISTS "permission_role" (
    "permission_id" BIGINT NOT NULL,
    "role_id" BIGINT NOT NULL,
    PRIMARY KEY ("permission_id", "role_id"),
    CONSTRAINT "permission_role_permission_id_foreign" FOREIGN KEY ("permission_id") REFERENCES "permissions" ("id") ON DELETE CASCADE,
    CONSTRAINT "permission_role_role_id_foreign" FOREIGN KEY ("role_id") REFERENCES "roles" ("id") ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS "permission_role_role_id_index" ON "permission_role" ("role_id");

-- =============================================================================
-- SECTION 2: LARAVEL FRAMEWORK SYSTEM TABLES (CACHE & QUEUE JOBS)
-- =============================================================================

-- Table: cache
-- Key-value cache storage for database cache driver.
-- Migration: database/migrations/0001_01_01_000001_create_cache_table.php
CREATE TABLE IF NOT EXISTS "cache" (
    "key" VARCHAR(255) PRIMARY KEY,
    "value" TEXT NOT NULL,
    "expiration" INTEGER NOT NULL
);

-- Table: cache_locks
-- Atomic lock storage for cache driver locks.
-- Migration: database/migrations/0001_01_01_000001_create_cache_table.php
CREATE TABLE IF NOT EXISTS "cache_locks" (
    "key" VARCHAR(255) PRIMARY KEY,
    "owner" VARCHAR(255) NOT NULL,
    "expiration" INTEGER NOT NULL
);

-- Table: jobs
-- Database queue job queue table.
-- Migration: database/migrations/0001_01_01_000002_create_jobs_table.php
CREATE TABLE IF NOT EXISTS "jobs" (
    "id" BIGSERIAL PRIMARY KEY,
    "queue" VARCHAR(255) NOT NULL,
    "payload" TEXT NOT NULL,
    "attempts" SMALLINT NOT NULL,
    "reserved_at" INTEGER NULL,
    "available_at" INTEGER NOT NULL,
    "created_at" INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS "jobs_queue_index" ON "jobs" ("queue");

-- Table: job_batches
-- Queue batch metadata and tracking.
-- Migration: database/migrations/0001_01_01_000002_create_jobs_table.php
CREATE TABLE IF NOT EXISTS "job_batches" (
    "id" VARCHAR(255) PRIMARY KEY,
    "name" VARCHAR(255) NOT NULL,
    "total_jobs" INTEGER NOT NULL,
    "pending_jobs" INTEGER NOT NULL,
    "failed_jobs" INTEGER NOT NULL,
    "failed_job_ids" TEXT NOT NULL,
    "options" TEXT NULL,
    "cancelled_at" INTEGER NULL,
    "created_at" INTEGER NOT NULL,
    "finished_at" INTEGER NULL
);

-- Table: failed_jobs
-- Record of failed queue jobs and exceptions.
-- Migration: database/migrations/0001_01_01_000002_create_jobs_table.php
CREATE TABLE IF NOT EXISTS "failed_jobs" (
    "id" BIGSERIAL PRIMARY KEY,
    "uuid" VARCHAR(255) NOT NULL,
    "connection" TEXT NOT NULL,
    "queue" TEXT NOT NULL,
    "payload" TEXT NOT NULL,
    "exception" TEXT NOT NULL,
    "failed_at" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT "failed_jobs_uuid_unique" UNIQUE ("uuid")
);

-- =============================================================================
-- SECTION 3: CLINIC CONFIGURATION & USER EXTENSIONS
-- =============================================================================

-- Table: clinic_settings
-- System-wide clinic brand, contact information, and doctor exclusions.
-- Migration: Modules/Setting/database/migrations/2026_01_01_000016_create_clinic_settings_table.php
CREATE TABLE IF NOT EXISTS "clinic_settings" (
    "id" BIGSERIAL PRIMARY KEY,
    "logo_path" VARCHAR(255) NULL,
    "khmer_title" VARCHAR(255) NULL,
    "khmer_title_color" VARCHAR(10) NOT NULL DEFAULT '#000000',
    "english_title" VARCHAR(255) NULL,
    "english_title_color" VARCHAR(10) NOT NULL DEFAULT '#000000',
    "description" TEXT NULL,
    "description_color" VARCHAR(10) NOT NULL DEFAULT '#555555',
    "facebook_account" VARCHAR(255) NULL,
    "facebook_page" VARCHAR(255) NULL,
    "email" VARCHAR(255) NULL,
    "phone" VARCHAR(255) NULL,
    "address" TEXT NULL,
    "except_doctor_ids" JSON NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL
);

-- Table: profiles
-- One-to-one personal profile extension for users.
-- Migration: Modules/Hospital/database/migrations/2026_01_01_000018_create_profiles_table.php
CREATE TABLE IF NOT EXISTS "profiles" (
    "id" BIGSERIAL PRIMARY KEY,
    "user_id" BIGINT NOT NULL,
    "first_name" VARCHAR(100) NULL,
    "last_name" VARCHAR(100) NULL,
    "gender" VARCHAR(20) NULL,
    "date_of_birth" DATE NULL,
    "phone" VARCHAR(30) NULL,
    "address" TEXT NULL,
    "photo" VARCHAR(255) NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "profiles_user_id_unique" UNIQUE ("user_id"),
    CONSTRAINT "profiles_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE
);

-- Table: doctors
-- One-to-one doctor professional profile extension for users.
-- Migration: Modules/Hospital/database/migrations/2026_01_01_000019_create_doctors_table.php
CREATE TABLE IF NOT EXISTS "doctors" (
    "id" BIGSERIAL PRIMARY KEY,
    "user_id" BIGINT NOT NULL,
    "hospital_id" BIGINT NULL,
    "doctor_code" VARCHAR(50) NOT NULL,
    "specialization" VARCHAR(255) NULL,
    "qualification" VARCHAR(255) NULL,
    "license_number" VARCHAR(100) NULL,
    "experience_years" INTEGER NOT NULL DEFAULT 0,
    "consultation_fee" NUMERIC(10, 2) NOT NULL DEFAULT 0.00,
    "bio" TEXT NULL,
    "status" BOOLEAN NOT NULL DEFAULT TRUE,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "doctors_user_id_unique" UNIQUE ("user_id"),
    CONSTRAINT "doctors_doctor_code_unique" UNIQUE ("doctor_code"),
    CONSTRAINT "doctors_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS "doctors_hospital_id_index" ON "doctors" ("hospital_id");
CREATE INDEX IF NOT EXISTS "doctors_specialization_index" ON "doctors" ("specialization");
CREATE INDEX IF NOT EXISTS "doctors_status_index" ON "doctors" ("status");

-- =============================================================================
-- SECTION 4: PATIENTS & CLINICAL DENTAL CHARTING
-- =============================================================================

-- Table: patients
-- Patient master record with medical history and soft delete capability.
-- Migration: Modules/Patient/database/migrations/2026_01_01_000001_create_patients_table.php
CREATE TABLE IF NOT EXISTS "patients" (
    "id" BIGSERIAL PRIMARY KEY,
    "patient_number" VARCHAR(255) NOT NULL,
    "full_name" VARCHAR(255) NOT NULL,
    "date_of_birth" DATE NULL,
    "sex" VARCHAR(255) NULL,
    "phone" VARCHAR(255) NULL,
    "address" TEXT NULL,
    "medical_notes" TEXT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "patients_patient_number_unique" UNIQUE ("patient_number")
);

-- Table: dental_charts
-- Master clinical examination chart for a patient visit.
-- Migration: Modules/DentalChart/database/migrations/2026_01_01_000002_create_dental_charts_table.php
CREATE TABLE IF NOT EXISTS "dental_charts" (
    "id" BIGSERIAL PRIMARY KEY,
    "patient_id" BIGINT NOT NULL,
    "chart_date" DATE NOT NULL,
    "dentition_type" VARCHAR(255) NOT NULL DEFAULT 'mixed',
    "examination_notes" TEXT NULL,
    "created_by" BIGINT NULL,
    "updated_by" BIGINT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "dental_charts_patient_id_foreign" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE CASCADE,
    CONSTRAINT "dental_charts_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "users" ("id") ON DELETE SET NULL,
    CONSTRAINT "dental_charts_updated_by_foreign" FOREIGN KEY ("updated_by") REFERENCES "users" ("id") ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "dental_charts_patient_id_chart_date_index" ON "dental_charts" ("patient_id", "chart_date");

-- Table: chart_teeth
-- Individual tooth status in a dental chart (FDI 11-48 adult, 51-85 primary).
-- Migration: Modules/DentalChart/database/migrations/2026_01_01_000003_create_chart_teeth_table.php
CREATE TABLE IF NOT EXISTS "chart_teeth" (
    "id" BIGSERIAL PRIMARY KEY,
    "dental_chart_id" BIGINT NOT NULL,
    "tooth_number" SMALLINT NOT NULL,
    "tooth_type" VARCHAR(255) NOT NULL,
    "tooth_status" VARCHAR(255) NOT NULL DEFAULT 'present',
    "general_notes" TEXT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "chart_teeth_dental_chart_id_tooth_number_unique" UNIQUE ("dental_chart_id", "tooth_number"),
    CONSTRAINT "chart_teeth_dental_chart_id_foreign" FOREIGN KEY ("dental_chart_id") REFERENCES "dental_charts" ("id") ON DELETE CASCADE
);

-- Table: dental_conditions
-- Diagnostic conditions, findings catalog, and color indicators.
-- Migration: Modules/DentalCondition/database/migrations/2026_01_01_000004_create_dental_conditions_table.php
CREATE TABLE IF NOT EXISTS "dental_conditions" (
    "id" BIGSERIAL PRIMARY KEY,
    "name" VARCHAR(255) NOT NULL,
    "code" VARCHAR(255) NOT NULL,
    "description" TEXT NULL,
    "default_color" VARCHAR(7) NOT NULL DEFAULT '#64748b',
    "category" VARCHAR(255) NOT NULL DEFAULT 'finding',
    "is_active" BOOLEAN NOT NULL DEFAULT TRUE,
    "created_by" BIGINT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "dental_conditions_code_unique" UNIQUE ("code"),
    CONSTRAINT "dental_conditions_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "users" ("id") ON DELETE SET NULL
);

-- Table: tooth_findings
-- Specific diagnosed surface conditions or treatments attached to a chart tooth.
-- Migration: Modules/ToothFinding/database/migrations/2026_01_01_000005_create_tooth_findings_table.php
CREATE TABLE IF NOT EXISTS "tooth_findings" (
    "id" BIGSERIAL PRIMARY KEY,
    "chart_tooth_id" BIGINT NOT NULL,
    "dental_condition_id" BIGINT NULL,
    "surface" VARCHAR(12) NOT NULL,
    "custom_color" VARCHAR(7) NULL,
    "note" TEXT NULL,
    "treatment_status" VARCHAR(255) NOT NULL DEFAULT 'observed',
    "created_by" BIGINT NULL,
    "updated_by" BIGINT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "tooth_findings_chart_tooth_id_foreign" FOREIGN KEY ("chart_tooth_id") REFERENCES "chart_teeth" ("id") ON DELETE CASCADE,
    CONSTRAINT "tooth_findings_dental_condition_id_foreign" FOREIGN KEY ("dental_condition_id") REFERENCES "dental_conditions" ("id") ON DELETE SET NULL,
    CONSTRAINT "tooth_findings_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "users" ("id") ON DELETE SET NULL,
    CONSTRAINT "tooth_findings_updated_by_foreign" FOREIGN KEY ("updated_by") REFERENCES "users" ("id") ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "tooth_findings_chart_tooth_id_surface_index" ON "tooth_findings" ("chart_tooth_id", "surface");

-- =============================================================================
-- SECTION 5: HOSPITAL CATALOG (CATEGORIES & SERVICES)
-- =============================================================================

-- Table: categories
-- Item classification for clinical services and products.
-- Migration: Modules/Hospital/database/migrations/2026_01_01_000013_create_categories_table.php
CREATE TABLE IF NOT EXISTS "categories" (
    "id" BIGSERIAL PRIMARY KEY,
    "hospital_id" BIGINT NULL,
    "name" VARCHAR(255) NOT NULL,
    "description" TEXT NULL,
    "type" VARCHAR(32) NOT NULL,
    "is_active" BOOLEAN NOT NULL DEFAULT TRUE,
    "code" VARCHAR(255) NOT NULL,
    "created_by" BIGINT NULL,
    "updated_by" BIGINT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL
);

CREATE INDEX IF NOT EXISTS "categories_hospital_id_index" ON "categories" ("hospital_id");
CREATE INDEX IF NOT EXISTS "categories_type_index" ON "categories" ("type");
CREATE INDEX IF NOT EXISTS "categories_code_index" ON "categories" ("code");
CREATE INDEX IF NOT EXISTS "categories_type_code_index" ON "categories" ("type", "code");
CREATE INDEX IF NOT EXISTS "categories_hospital_id_type_index" ON "categories" ("hospital_id", "type");

-- Table: services
-- Chargeable dental treatments, procedures, and clinic products.
-- Migration: Modules/Hospital/database/migrations/2026_01_01_000014_create_services_table.php
CREATE TABLE IF NOT EXISTS "services" (
    "id" BIGSERIAL PRIMARY KEY,
    "hospital_id" BIGINT NULL,
    "category_id" BIGINT NULL,
    "code" VARCHAR(255) NOT NULL,
    "name" VARCHAR(255) NOT NULL,
    "description" TEXT NULL,
    "price" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "cost" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "discount" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "type" VARCHAR(32) NOT NULL,
    "qty" INTEGER NOT NULL DEFAULT 0,
    "supplier_id" BIGINT NULL,
    "commission" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "commission_type" VARCHAR(32) NOT NULL DEFAULT 'fixed',
    "status" VARCHAR(32) NOT NULL DEFAULT 'active',
    "created_by" BIGINT NULL,
    "updated_by" BIGINT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "services_category_id_foreign" FOREIGN KEY ("category_id") REFERENCES "categories" ("id") ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "services_hospital_id_index" ON "services" ("hospital_id");
CREATE INDEX IF NOT EXISTS "services_category_id_index" ON "services" ("category_id");
CREATE INDEX IF NOT EXISTS "services_code_index" ON "services" ("code");
CREATE INDEX IF NOT EXISTS "services_type_index" ON "services" ("type");
CREATE INDEX IF NOT EXISTS "services_type_code_index" ON "services" ("type", "code");
CREATE INDEX IF NOT EXISTS "services_hospital_id_type_index" ON "services" ("hospital_id", "type");

-- =============================================================================
-- SECTION 6: BILLING, RECEPTION, PAYMENTS & INVOICE ITEMS
-- =============================================================================

-- Table: invoices
-- Billing invoices for patient consultations, procedures, and items.
-- Migrations:
--   - Modules/Invoice/database/migrations/2026_01_01_000010_create_invoices_table.php
--   - Modules/Invoice/database/migrations/2026_01_01_000015_create_invoice_items_table.php (added subtotal, discount, tax)
CREATE TABLE IF NOT EXISTS "invoices" (
    "id" BIGSERIAL PRIMARY KEY,
    "invoice_number" VARCHAR(255) NOT NULL,
    "patient_id" BIGINT NOT NULL,
    "invoice_date" DATE NOT NULL,
    "subtotal" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "discount" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "tax" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "total_amount" NUMERIC(10, 2) NOT NULL DEFAULT 0.00,
    "paid_amount" NUMERIC(10, 2) NOT NULL DEFAULT 0.00,
    "balance_amount" NUMERIC(10, 2) NOT NULL DEFAULT 0.00,
    "status" VARCHAR(30) NOT NULL DEFAULT 'unpaid',
    "notes" TEXT NULL,
    "created_by" BIGINT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "invoices_invoice_number_unique" UNIQUE ("invoice_number"),
    CONSTRAINT "invoices_patient_id_foreign" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE CASCADE,
    CONSTRAINT "invoices_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "users" ("id") ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "invoices_patient_id_index" ON "invoices" ("patient_id");
CREATE INDEX IF NOT EXISTS "invoices_status_index" ON "invoices" ("status");
CREATE INDEX IF NOT EXISTS "invoices_invoice_date_index" ON "invoices" ("invoice_date");

-- Table: receptions
-- Patient clinic registrations, check-in visits, and linked invoices.
-- Migration: Modules/Register/database/migrations/2026_01_01_000011_create_receptions_table.php
CREATE TABLE IF NOT EXISTS "receptions" (
    "id" BIGSERIAL PRIMARY KEY,
    "reception_number" VARCHAR(255) NOT NULL,
    "patient_id" BIGINT NOT NULL,
    "invoice_id" BIGINT NOT NULL,
    "reception_date" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    "visit_type" VARCHAR(30) NOT NULL DEFAULT 'initial',
    "chief_complaint" TEXT NULL,
    "status" VARCHAR(30) NOT NULL DEFAULT 'checked_in',
    "notes" TEXT NULL,
    "created_by" BIGINT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "receptions_reception_number_unique" UNIQUE ("reception_number"),
    CONSTRAINT "receptions_patient_id_foreign" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE CASCADE,
    CONSTRAINT "receptions_invoice_id_foreign" FOREIGN KEY ("invoice_id") REFERENCES "invoices" ("id") ON DELETE CASCADE,
    CONSTRAINT "receptions_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "users" ("id") ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "receptions_patient_id_index" ON "receptions" ("patient_id");
CREATE INDEX IF NOT EXISTS "receptions_invoice_id_index" ON "receptions" ("invoice_id");
CREATE INDEX IF NOT EXISTS "receptions_reception_date_index" ON "receptions" ("reception_date");
CREATE INDEX IF NOT EXISTS "receptions_status_index" ON "receptions" ("status");

-- Table: payments
-- Payment receipts against patient invoices.
-- Migration: Modules/Payment/database/migrations/2026_01_01_000012_create_payments_table.php
CREATE TABLE IF NOT EXISTS "payments" (
    "id" BIGSERIAL PRIMARY KEY,
    "payment_number" VARCHAR(255) NOT NULL,
    "invoice_id" BIGINT NOT NULL,
    "patient_id" BIGINT NOT NULL,
    "payment_date" DATE NOT NULL,
    "amount" NUMERIC(10, 2) NOT NULL,
    "currency" VARCHAR(10) NOT NULL DEFAULT 'USD',
    "payment_method" VARCHAR(30) NOT NULL DEFAULT 'cash',
    "transaction_reference" VARCHAR(100) NULL,
    "status" VARCHAR(30) NOT NULL DEFAULT 'completed',
    "notes" TEXT NULL,
    "created_by" BIGINT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "payments_payment_number_unique" UNIQUE ("payment_number"),
    CONSTRAINT "payments_invoice_id_foreign" FOREIGN KEY ("invoice_id") REFERENCES "invoices" ("id") ON DELETE CASCADE,
    CONSTRAINT "payments_patient_id_foreign" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE CASCADE,
    CONSTRAINT "payments_created_by_foreign" FOREIGN KEY ("created_by") REFERENCES "users" ("id") ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "payments_invoice_id_index" ON "payments" ("invoice_id");
CREATE INDEX IF NOT EXISTS "payments_patient_id_index" ON "payments" ("patient_id");
CREATE INDEX IF NOT EXISTS "payments_payment_date_index" ON "payments" ("payment_date");
CREATE INDEX IF NOT EXISTS "payments_status_index" ON "payments" ("status");

-- Table: invoice_items
-- Detailed line items (procedures, products) attached to invoices.
-- Migration: Modules/Invoice/database/migrations/2026_01_01_000015_create_invoice_items_table.php
CREATE TABLE IF NOT EXISTS "invoice_items" (
    "id" BIGSERIAL PRIMARY KEY,
    "invoice_id" BIGINT NOT NULL,
    "type" VARCHAR(32) NOT NULL,
    "service_id" BIGINT NULL,
    "reception_id" BIGINT NULL,
    "item_name" VARCHAR(255) NOT NULL,
    "operate_date" DATE NOT NULL,
    "tooth_numbers" VARCHAR(255) NULL,
    "quantity" INTEGER NOT NULL DEFAULT 1,
    "unit_price" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "discount" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "tax" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "amount" NUMERIC(15, 2) NOT NULL DEFAULT 0.00,
    "note" TEXT NULL,
    "created_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    "deleted_at" TIMESTAMP(0) WITHOUT TIME ZONE NULL,
    CONSTRAINT "invoice_items_invoice_id_foreign" FOREIGN KEY ("invoice_id") REFERENCES "invoices" ("id") ON DELETE CASCADE,
    CONSTRAINT "invoice_items_service_id_foreign" FOREIGN KEY ("service_id") REFERENCES "services" ("id") ON DELETE SET NULL,
    CONSTRAINT "invoice_items_reception_id_foreign" FOREIGN KEY ("reception_id") REFERENCES "receptions" ("id") ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "invoice_items_invoice_id_type_index" ON "invoice_items" ("invoice_id", "type");

COMMIT;

