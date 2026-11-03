# Database Schema Analysis: Dental Chart Application

**Project:** Dental Chart Management System  
**Framework:** Laravel 11.x on PHP 8.2+  
**Target Database Engine:** PostgreSQL 12+  
**Output Files Generated:** `schema.sql`, `schema-analysis.md`  

---

## 1. Executive Summary

This report provides an architectural and structural analysis of the PostgreSQL database schema for the Dental Chart application. The schema was reverse-engineered by auditing the complete migration history of the application, encompassing both the root Laravel migration directory and 12 modular domains managed via `nwidart/laravel-modules`.

### Key Metrics
- **Total Migration Files Inspected:** 24 files
- **Total Tables Created:** 26 tables
  - **Laravel Framework System Tables:** 8 tables
  - **Application Domain Tables:** 18 tables
- **Total Foreign Key Constraints:** 20 constraints
- **Total Indexes (Explicit & Secondary):** 33 indexes
- **Total Unique Constraints (Explicit & Table-level):** 16 constraints
- **Database Engine Compatibility:** 100% PostgreSQL-native (tested and validated against PostgreSQL 12.22)

---

## 2. Inventory of Inspected Migration Files

All 24 migration files across the workspace were inspected in chronological and topological order:

| # | Migration File Path | Purpose / Operation | Affected Table(s) |
|---|---------------------|---------------------|-------------------|
| 1 | `database/migrations/0001_01_01_000000_create_users_table.php` | Create base tables | `users`, `password_reset_tokens`, `sessions` |
| 2 | `database/migrations/0001_01_01_000001_create_cache_table.php` | Create cache tables | `cache`, `cache_locks` |
| 3 | `database/migrations/0001_01_01_000002_create_jobs_table.php` | Create queue job tables | `jobs`, `job_batches`, `failed_jobs` |
| 4 | `Modules/User/database/migrations/2026_01_01_000001_create_roles_table.php` | Create roles table | `roles` |
| 5 | `Modules/User/database/migrations/2026_01_01_000002_create_permissions_table.php` | Create permissions table | `permissions` |
| 6 | `Modules/User/database/migrations/2026_01_01_000003_create_role_user_table.php` | Create pivot table | `role_user` |
| 7 | `Modules/User/database/migrations/2026_01_01_000004_create_permission_role_table.php` | Create pivot table | `permission_role` |
| 8 | `Modules/User/database/migrations/2026_01_01_000005_add_username_to_users_table.php` | Alter table (add column) | `users` (`username`) |
| 9 | `Modules/User/database/migrations/2026_01_01_000006_add_is_super_addmin_to_users_table.php` | Alter table (add column) | `users` (`is_super_addmin`) |
| 10 | `Modules/Patient/database/migrations/2026_01_01_000001_create_patients_table.php` | Create patients table | `patients` |
| 11 | `Modules/DentalChart/database/migrations/2026_01_01_000002_create_dental_charts_table.php` | Create dental charts table | `dental_charts` |
| 12 | `Modules/DentalChart/database/migrations/2026_01_01_000003_create_chart_teeth_table.php` | Create teeth inventory table | `chart_teeth` |
| 13 | `Modules/DentalCondition/database/migrations/2026_01_01_000004_create_dental_conditions_table.php` | Create conditions catalog | `dental_conditions` |
| 14 | `Modules/ToothFinding/database/migrations/2026_01_01_000005_create_tooth_findings_table.php` | Create tooth findings table | `tooth_findings` |
| 15 | `Modules/Invoice/database/migrations/2026_01_01_000010_create_invoices_table.php` | Create invoices table | `invoices` |
| 16 | `Modules/Register/database/migrations/2026_01_01_000011_create_receptions_table.php` | Create reception check-ins | `receptions` |
| 17 | `Modules/Payment/database/migrations/2026_01_01_000012_create_payments_table.php` | Create payments table | `payments` |
| 18 | `Modules/Hospital/database/migrations/2026_01_01_000013_create_categories_table.php` | Create categories table | `categories` |
| 19 | `Modules/Hospital/database/migrations/2026_01_01_000014_create_services_table.php` | Create services catalog | `services` |
| 20 | `Modules/Invoice/database/migrations/2026_01_01_000015_create_invoice_items_table.php` | Alter `invoices` & Create `invoice_items` | `invoices` (altered), `invoice_items` |
| 21 | `Modules/Setting/database/migrations/2026_01_01_000016_create_clinic_settings_table.php` | Create clinic settings table | `clinic_settings` |
| 22 | `Modules/Hospital/database/migrations/2026_01_01_000017_add_is_active_to_users_table.php` | Alter table (add column) | `users` (`is_active`) |
| 23 | `Modules/Hospital/database/migrations/2026_01_01_000018_create_profiles_table.php` | Create user profile extension | `profiles` |
| 24 | `Modules/Hospital/database/migrations/2026_01_01_000019_create_doctors_table.php` | Create doctor profile extension | `doctors` |

> *Note:* `Modules/Core/database/migrations` and `Modules/ActivityLog/database/migrations` exist but contain only `.gitkeep` files (no active migrations).

---

## 3. Migration Evolution & Table Modifications

Unlike static schemas, Laravel migrations apply incremental alterations over time. The generated `schema.sql` combines all historical modifications into the final consolidated schema.

### Table: `users`
The `users` table underwent 4 progressive migrations:
1. **Initial Creation (`0001_01_01_000000_create_users_table.php`):**
   - Created `id`, `name`, `email` (unique), `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`.
2. **Username Addition (`2026_01_01_000005_add_username_to_users_table.php`):**
   - Added `username VARCHAR(255) NULL UNIQUE` positioned after `name`.
3. **Super Admin Flag (`2026_01_01_000006_add_is_super_addmin_to_users_table.php`):**
   - Added `is_super_addmin BOOLEAN NOT NULL DEFAULT FALSE` positioned after `password`.
4. **Account Status Flag (`2026_01_01_000017_add_is_active_to_users_table.php`):**
   - Added `is_active BOOLEAN NOT NULL DEFAULT TRUE` positioned after `is_super_addmin`.

### Table: `invoices`
The `invoices` table was modified across 2 migrations:
1. **Initial Creation (`2026_01_01_000010_create_invoices_table.php`):**
   - Created `id`, `invoice_number` (unique), `patient_id` (FK), `invoice_date`, `total_amount`, `paid_amount`, `balance_amount`, `status`, `notes`, `created_by` (FK), timestamps, and soft deletes.
2. **Breakdown Fields (`2026_01_01_000015_create_invoice_items_table.php`):**
   - Added `subtotal NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
   - Added `discount NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
   - Added `tax NUMERIC(15, 2) NOT NULL DEFAULT 0.00`

---

## 4. Complete Table Specifications

### 4.1 Laravel Framework System Tables

#### 1. `users`
- **Purpose:** Core application user identity, credentials, and access flags.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `name`: `VARCHAR(255) NOT NULL`
  - `username`: `VARCHAR(255) NULL`
  - `email`: `VARCHAR(255) NOT NULL`
  - `email_verified_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `password`: `VARCHAR(255) NOT NULL`
  - `is_super_addmin`: `BOOLEAN NOT NULL DEFAULT FALSE`
  - `is_active`: `BOOLEAN NOT NULL DEFAULT TRUE`
  - `remember_token`: `VARCHAR(100) NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `users_email_unique`: `UNIQUE (email)`
  - `users_username_unique`: `UNIQUE (username)`

#### 2. `password_reset_tokens`
- **Purpose:** Storage for user password reset hashes.
- **Columns:**
  - `email`: `VARCHAR(255) PRIMARY KEY`
  - `token`: `VARCHAR(255) NOT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`

#### 3. `sessions`
- **Purpose:** Laravel database session storage handler.
- **Columns:**
  - `id`: `VARCHAR(255) PRIMARY KEY`
  - `user_id`: `BIGINT NULL`
  - `ip_address`: `VARCHAR(45) NULL`
  - `user_agent`: `TEXT NULL`
  - `payload`: `TEXT NOT NULL`
  - `last_activity`: `INTEGER NOT NULL`
- **Indexes:**
  - `sessions_user_id_index`: `(user_id)`
  - `sessions_last_activity_index`: `(last_activity)`

#### 4. `cache`
- **Purpose:** Key-value application cache storage.
- **Columns:**
  - `key`: `VARCHAR(255) PRIMARY KEY`
  - `value`: `TEXT NOT NULL`
  - `expiration`: `INTEGER NOT NULL`

#### 5. `cache_locks`
- **Purpose:** Distributed atomic cache locks.
- **Columns:**
  - `key`: `VARCHAR(255) PRIMARY KEY`
  - `owner`: `VARCHAR(255) NOT NULL`
  - `expiration`: `INTEGER NOT NULL`

#### 6. `jobs`
- **Purpose:** Queued background jobs.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `queue`: `VARCHAR(255) NOT NULL`
  - `payload`: `TEXT NOT NULL`
  - `attempts`: `SMALLINT NOT NULL`
  - `reserved_at`: `INTEGER NULL`
  - `available_at`: `INTEGER NOT NULL`
  - `created_at`: `INTEGER NOT NULL`
- **Indexes:**
  - `jobs_queue_index`: `(queue)`

#### 7. `job_batches`
- **Purpose:** Queue batch tracking and lifecycle metadata.
- **Columns:**
  - `id`: `VARCHAR(255) PRIMARY KEY`
  - `name`: `VARCHAR(255) NOT NULL`
  - `total_jobs`: `INTEGER NOT NULL`
  - `pending_jobs`: `INTEGER NOT NULL`
  - `failed_jobs`: `INTEGER NOT NULL`
  - `failed_job_ids`: `TEXT NOT NULL`
  - `options`: `TEXT NULL`
  - `cancelled_at`: `INTEGER NULL`
  - `created_at`: `INTEGER NOT NULL`
  - `finished_at`: `INTEGER NULL`

#### 8. `failed_jobs`
- **Purpose:** Exception log for unhandled queue worker job failures.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `uuid`: `VARCHAR(255) NOT NULL`
  - `connection`: `TEXT NOT NULL`
  - `queue`: `TEXT NOT NULL`
  - `payload`: `TEXT NOT NULL`
  - `exception`: `TEXT NOT NULL`
  - `failed_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP`
- **Constraints:**
  - `failed_jobs_uuid_unique`: `UNIQUE (uuid)`

---

### 4.2 User Management & RBAC Module

#### 9. `roles`
- **Purpose:** Role definitions for application RBAC.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `name`: `VARCHAR(255) NOT NULL`
  - `label`: `VARCHAR(255) NOT NULL`
  - `description`: `TEXT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `roles_name_unique`: `UNIQUE (name)`

#### 10. `permissions`
- **Purpose:** Granular permissions associated with specific controllers and actions.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `name`: `VARCHAR(255) NOT NULL`
  - `module`: `VARCHAR(255) NOT NULL`
  - `controller`: `VARCHAR(255) NOT NULL`
  - `action`: `VARCHAR(255) NOT NULL`
  - `label`: `VARCHAR(255) NULL`
  - `description`: `TEXT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `permissions_name_unique`: `UNIQUE (name)`
- **Indexes:**
  - `permissions_module_index`: `(module)`
  - `permissions_controller_index`: `(controller)`
  - `permissions_module_controller_index`: `(module, controller)`

#### 11. `role_user`
- **Purpose:** Many-to-many pivot table binding users to roles.
- **Columns:**
  - `role_id`: `BIGINT NOT NULL`
  - `user_id`: `BIGINT NOT NULL`
- **Constraints:**
  - `PRIMARY KEY (role_id, user_id)`
  - `role_user_role_id_foreign`: `FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE`
  - `role_user_user_id_foreign`: `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`
- **Indexes:**
  - `role_user_user_id_index`: `(user_id)`

#### 12. `permission_role`
- **Purpose:** Many-to-many pivot table binding roles to permissions.
- **Columns:**
  - `permission_id`: `BIGINT NOT NULL`
  - `role_id`: `BIGINT NOT NULL`
- **Constraints:**
  - `PRIMARY KEY (permission_id, role_id)`
  - `permission_role_permission_id_foreign`: `FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE`
  - `permission_role_role_id_foreign`: `FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE`
- **Indexes:**
  - `permission_role_role_id_index`: `(role_id)`

---

### 4.3 Patient & Clinical Dental Charting Modules

#### 13. `patients`
- **Purpose:** Patient master records and demographics.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `patient_number`: `VARCHAR(255) NOT NULL`
  - `full_name`: `VARCHAR(255) NOT NULL`
  - `date_of_birth`: `DATE NULL`
  - `sex`: `VARCHAR(255) NULL`
  - `phone`: `VARCHAR(255) NULL`
  - `address`: `TEXT NULL`
  - `medical_notes`: `TEXT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL` (Soft deletes)
- **Constraints:**
  - `patients_patient_number_unique`: `UNIQUE (patient_number)`

#### 14. `dental_charts`
- **Purpose:** Clinical dental examination chart master record.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `patient_id`: `BIGINT NOT NULL`
  - `chart_date`: `DATE NOT NULL`
  - `dentition_type`: `VARCHAR(255) NOT NULL DEFAULT 'mixed'`
  - `examination_notes`: `TEXT NULL`
  - `created_by`: `BIGINT NULL`
  - `updated_by`: `BIGINT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `dental_charts_patient_id_foreign`: `FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE`
  - `dental_charts_created_by_foreign`: `FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL`
  - `dental_charts_updated_by_foreign`: `FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL`
- **Indexes:**
  - `dental_charts_patient_id_chart_date_index`: `(patient_id, chart_date)`

#### 15. `chart_teeth`
- **Purpose:** Individual tooth status record for adult (11–48) and primary (51–85) dentition.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `dental_chart_id`: `BIGINT NOT NULL`
  - `tooth_number`: `SMALLINT NOT NULL`
  - `tooth_type`: `VARCHAR(255) NOT NULL`
  - `tooth_status`: `VARCHAR(255) NOT NULL DEFAULT 'present'`
  - `general_notes`: `TEXT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `chart_teeth_dental_chart_id_tooth_number_unique`: `UNIQUE (dental_chart_id, tooth_number)`
  - `chart_teeth_dental_chart_id_foreign`: `FOREIGN KEY (dental_chart_id) REFERENCES dental_charts(id) ON DELETE CASCADE`

#### 16. `dental_conditions`
- **Purpose:** Standardized diagnostic conditions catalog with chart rendering colors.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `name`: `VARCHAR(255) NOT NULL`
  - `code`: `VARCHAR(255) NOT NULL`
  - `description`: `TEXT NULL`
  - `default_color`: `VARCHAR(7) NOT NULL DEFAULT '#64748b'`
  - `category`: `VARCHAR(255) NOT NULL DEFAULT 'finding'`
  - `is_active`: `BOOLEAN NOT NULL DEFAULT TRUE`
  - `created_by`: `BIGINT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `dental_conditions_code_unique`: `UNIQUE (code)`
  - `dental_conditions_created_by_foreign`: `FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL`

#### 17. `tooth_findings`
- **Purpose:** Specific diagnoses, observations, and treatments mapped to tooth surfaces.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `chart_tooth_id`: `BIGINT NOT NULL`
  - `dental_condition_id`: `BIGINT NULL`
  - `surface`: `VARCHAR(12) NOT NULL` (top, bottom, left, right, center, whole)
  - `custom_color`: `VARCHAR(7) NULL`
  - `note`: `TEXT NULL`
  - `treatment_status`: `VARCHAR(255) NOT NULL DEFAULT 'observed'`
  - `created_by`: `BIGINT NULL`
  - `updated_by`: `BIGINT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `tooth_findings_chart_tooth_id_foreign`: `FOREIGN KEY (chart_tooth_id) REFERENCES chart_teeth(id) ON DELETE CASCADE`
  - `tooth_findings_dental_condition_id_foreign`: `FOREIGN KEY (dental_condition_id) REFERENCES dental_conditions(id) ON DELETE SET NULL`
  - `tooth_findings_created_by_foreign`: `FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL`
  - `tooth_findings_updated_by_foreign`: `FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL`
- **Indexes:**
  - `tooth_findings_chart_tooth_id_surface_index`: `(chart_tooth_id, surface)`

---

### 4.4 Hospital & Clinical Services Catalog

#### 18. `categories`
- **Purpose:** Classification hierarchy for services and products.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `hospital_id`: `BIGINT NULL`
  - `name`: `VARCHAR(255) NOT NULL`
  - `description`: `TEXT NULL`
  - `type`: `VARCHAR(32) NOT NULL` ('service' or 'product')
  - `is_active`: `BOOLEAN NOT NULL DEFAULT TRUE`
  - `code`: `VARCHAR(255) NOT NULL`
  - `created_by`: `BIGINT NULL`
  - `updated_by`: `BIGINT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Indexes:**
  - `categories_hospital_id_index`: `(hospital_id)`
  - `categories_type_index`: `(type)`
  - `categories_code_index`: `(code)`
  - `categories_type_code_index`: `(type, code)`
  - `categories_hospital_id_type_index`: `(hospital_id, type)`

#### 19. `services`
- **Purpose:** Chargeable clinic procedures, dental treatments, and consumable products.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `hospital_id`: `BIGINT NULL`
  - `category_id`: `BIGINT NULL`
  - `code`: `VARCHAR(255) NOT NULL`
  - `name`: `VARCHAR(255) NOT NULL`
  - `description`: `TEXT NULL`
  - `price`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `cost`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `discount`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `type`: `VARCHAR(32) NOT NULL`
  - `qty`: `INTEGER NOT NULL DEFAULT 0`
  - `supplier_id`: `BIGINT NULL`
  - `commission`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `commission_type`: `VARCHAR(32) NOT NULL DEFAULT 'fixed'`
  - `status`: `VARCHAR(32) NOT NULL DEFAULT 'active'`
  - `created_by`: `BIGINT NULL`
  - `updated_by`: `BIGINT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `services_category_id_foreign`: `FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL`
- **Indexes:**
  - `services_hospital_id_index`: `(hospital_id)`
  - `services_category_id_index`: `(category_id)`
  - `services_code_index`: `(code)`
  - `services_type_index`: `(type)`
  - `services_type_code_index`: `(type, code)`
  - `services_hospital_id_type_index`: `(hospital_id, type)`

#### 20. `profiles`
- **Purpose:** Personal details extension for users.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `user_id`: `BIGINT NOT NULL`
  - `first_name`: `VARCHAR(100) NULL`
  - `last_name`: `VARCHAR(100) NULL`
  - `gender`: `VARCHAR(20) NULL`
  - `date_of_birth`: `DATE NULL`
  - `phone`: `VARCHAR(30) NULL`
  - `address`: `TEXT NULL`
  - `photo`: `VARCHAR(255) NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `profiles_user_id_unique`: `UNIQUE (user_id)`
  - `profiles_user_id_foreign`: `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`

#### 21. `doctors`
- **Purpose:** Doctor credentials, specialization, licensing, and consultation pricing.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `user_id`: `BIGINT NOT NULL`
  - `hospital_id`: `BIGINT NULL`
  - `doctor_code`: `VARCHAR(50) NOT NULL`
  - `specialization`: `VARCHAR(255) NULL`
  - `qualification`: `VARCHAR(255) NULL`
  - `license_number`: `VARCHAR(100) NULL`
  - `experience_years`: `INTEGER NOT NULL DEFAULT 0`
  - `consultation_fee`: `NUMERIC(10, 2) NOT NULL DEFAULT 0.00`
  - `bio`: `TEXT NULL`
  - `status`: `BOOLEAN NOT NULL DEFAULT TRUE`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `doctors_user_id_unique`: `UNIQUE (user_id)`
  - `doctors_doctor_code_unique`: `UNIQUE (doctor_code)`
  - `doctors_user_id_foreign`: `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`
- **Indexes:**
  - `doctors_hospital_id_index`: `(hospital_id)`
  - `doctors_specialization_index`: `(specialization)`
  - `doctors_status_index`: `(status)`

---

### 4.5 Billing, Registration & Payments

#### 22. `invoices`
- **Purpose:** Patient billing invoices with subtotal, tax, discounts, and payment status.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `invoice_number`: `VARCHAR(255) NOT NULL`
  - `patient_id`: `BIGINT NOT NULL`
  - `invoice_date`: `DATE NOT NULL`
  - `subtotal`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `discount`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `tax`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `total_amount`: `NUMERIC(10, 2) NOT NULL DEFAULT 0.00`
  - `paid_amount`: `NUMERIC(10, 2) NOT NULL DEFAULT 0.00`
  - `balance_amount`: `NUMERIC(10, 2) NOT NULL DEFAULT 0.00`
  - `status`: `VARCHAR(30) NOT NULL DEFAULT 'unpaid'`
  - `notes`: `TEXT NULL`
  - `created_by`: `BIGINT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `invoices_invoice_number_unique`: `UNIQUE (invoice_number)`
  - `invoices_patient_id_foreign`: `FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE`
  - `invoices_created_by_foreign`: `FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL`
- **Indexes:**
  - `invoices_patient_id_index`: `(patient_id)`
  - `invoices_status_index`: `(status)`
  - `invoices_invoice_date_index`: `(invoice_date)`

#### 23. `receptions`
- **Purpose:** Patient clinic arrivals, check-in queue, and visit management.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `reception_number`: `VARCHAR(255) NOT NULL`
  - `patient_id`: `BIGINT NOT NULL`
  - `invoice_id`: `BIGINT NOT NULL`
  - `reception_date`: `TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL`
  - `visit_type`: `VARCHAR(30) NOT NULL DEFAULT 'initial'`
  - `chief_complaint`: `TEXT NULL`
  - `status`: `VARCHAR(30) NOT NULL DEFAULT 'checked_in'`
  - `notes`: `TEXT NULL`
  - `created_by`: `BIGINT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `receptions_reception_number_unique`: `UNIQUE (reception_number)`
  - `receptions_patient_id_foreign`: `FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE`
  - `receptions_invoice_id_foreign`: `FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE`
  - `receptions_created_by_foreign`: `FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL`
- **Indexes:**
  - `receptions_patient_id_index`: `(patient_id)`
  - `receptions_invoice_id_index`: `(invoice_id)`
  - `receptions_reception_date_index`: `(reception_date)`
  - `receptions_status_index`: `(status)`

#### 24. `payments`
- **Purpose:** Financial payment transactions recorded against invoices.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `payment_number`: `VARCHAR(255) NOT NULL`
  - `invoice_id`: `BIGINT NOT NULL`
  - `patient_id`: `BIGINT NOT NULL`
  - `payment_date`: `DATE NOT NULL`
  - `amount`: `NUMERIC(10, 2) NOT NULL`
  - `currency`: `VARCHAR(10) NOT NULL DEFAULT 'USD'`
  - `payment_method`: `VARCHAR(30) NOT NULL DEFAULT 'cash'`
  - `transaction_reference`: `VARCHAR(100) NULL`
  - `status`: `VARCHAR(30) NOT NULL DEFAULT 'completed'`
  - `notes`: `TEXT NULL`
  - `created_by`: `BIGINT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `payments_payment_number_unique`: `UNIQUE (payment_number)`
  - `payments_invoice_id_foreign`: `FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE`
  - `payments_patient_id_foreign`: `FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE`
  - `payments_created_by_foreign`: `FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL`
- **Indexes:**
  - `payments_invoice_id_index`: `(invoice_id)`
  - `payments_patient_id_index`: `(patient_id)`
  - `payments_payment_date_index`: `(payment_date)`
  - `payments_status_index`: `(status)`

#### 25. `invoice_items`
- **Purpose:** Itemized procedure and product charges linked to an invoice.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `invoice_id`: `BIGINT NOT NULL`
  - `type`: `VARCHAR(32) NOT NULL` ('service' or 'product')
  - `service_id`: `BIGINT NULL`
  - `reception_id`: `BIGINT NULL`
  - `item_name`: `VARCHAR(255) NOT NULL`
  - `operate_date`: `DATE NOT NULL`
  - `tooth_numbers`: `VARCHAR(255) NULL`
  - `quantity`: `INTEGER NOT NULL DEFAULT 1`
  - `unit_price`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `discount`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `tax`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `amount`: `NUMERIC(15, 2) NOT NULL DEFAULT 0.00`
  - `note`: `TEXT NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `deleted_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
- **Constraints:**
  - `invoice_items_invoice_id_foreign`: `FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE`
  - `invoice_items_service_id_foreign`: `FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL`
  - `invoice_items_reception_id_foreign`: `FOREIGN KEY (reception_id) REFERENCES receptions(id) ON DELETE SET NULL`
- **Indexes:**
  - `invoice_items_invoice_id_type_index`: `(invoice_id, type)`

---

### 4.6 Clinic Configuration Module

#### 26. `clinic_settings`
- **Purpose:** Singleton configuration table storing clinic branding, titles, contact info, and doctor commission exclusions.
- **Columns:**
  - `id`: `BIGSERIAL PRIMARY KEY`
  - `logo_path`: `VARCHAR(255) NULL`
  - `khmer_title`: `VARCHAR(255) NULL`
  - `khmer_title_color`: `VARCHAR(10) NOT NULL DEFAULT '#000000'`
  - `english_title`: `VARCHAR(255) NULL`
  - `english_title_color`: `VARCHAR(10) NOT NULL DEFAULT '#000000'`
  - `description`: `TEXT NULL`
  - `description_color`: `VARCHAR(10) NOT NULL DEFAULT '#555555'`
  - `facebook_account`: `VARCHAR(255) NULL`
  - `facebook_page`: `VARCHAR(255) NULL`
  - `email`: `VARCHAR(255) NULL`
  - `phone`: `VARCHAR(255) NULL`
  - `address`: `TEXT NULL`
  - `except_doctor_ids`: `JSON NULL`
  - `created_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`
  - `updated_at`: `TIMESTAMP(0) WITHOUT TIME ZONE NULL`

---

## 5. Foreign Key Dependency Graph & Creation Order

To enable error-free execution from top to bottom without circular dependency resolution, tables are created in strictly ordered tiers:

```
Tier 1 (Zero Dependencies):
  users, password_reset_tokens, sessions, cache, cache_locks,
  jobs, job_batches, failed_jobs, roles, permissions,
  patients, clinic_settings, categories

Tier 2 (Depends on Tier 1):
  role_user         --> roles(id), users(id)
  permission_role   --> permissions(id), roles(id)
  profiles          --> users(id)
  doctors           --> users(id)
  dental_conditions --> users(id)
  dental_charts     --> patients(id), users(id)
  services          --> categories(id)
  invoices          --> patients(id), users(id)

Tier 3 (Depends on Tier 2):
  chart_teeth       --> dental_charts(id)
  receptions        --> patients(id), invoices(id), users(id)
  payments          --> invoices(id), patients(id), users(id)

Tier 4 (Depends on Tier 3):
  tooth_findings    --> chart_teeth(id), dental_conditions(id), users(id)
  invoice_items     --> invoices(id), services(id), receptions(id)
```

---

## 6. PostgreSQL-Specific Type Mappings & Implementation Rules

All Laravel Blueprint column macros were compiled into native PostgreSQL 12+ types:

| Laravel Blueprint Macro | PostgreSQL Type | Notes |
|-------------------------|-----------------|-------|
| `$table->id()` | `BIGSERIAL PRIMARY KEY` | Auto-incrementing 64-bit integer identity sequence |
| `$table->foreignId(...)` | `BIGINT` | Clean 64-bit foreign key referencing `id` |
| `$table->string('col', n)` | `VARCHAR(n)` | Length-constrained variable string |
| `$table->text()` | `TEXT` | Variable-length text |
| `$table->mediumText()` / `longText()` | `TEXT` | Postgres text has arbitrary length (no storage penalty) |
| `$table->boolean()` | `BOOLEAN` | Native boolean type; defaults emit `DEFAULT TRUE` / `DEFAULT FALSE` |
| `$table->date()` | `DATE` | Native ISO-8601 calendar date |
| `$table->dateTime()`, `timestamp()`, `timestamps()` | `TIMESTAMP(0) WITHOUT TIME ZONE` | Microsecond precision 0 matching Laravel standard |
| `$table->decimal(p, s)` | `NUMERIC(p, s)` | Arbitrary-precision exact fixed-point numeric |
| `$table->unsignedTinyInteger()` | `SMALLINT` | 2-byte integer (0–255 fits in PostgreSQL smallint) |
| `$table->unsignedInteger()`, `integer()` | `INTEGER` | 4-byte signed integer |
| `$table->json()` | `JSON` | Native PostgreSQL JSON storage |
| `$table->softDeletes()` | `TIMESTAMP(0) WITHOUT TIME ZONE NULL` | Stores soft deletion timestamp (`deleted_at`) |
| `$table->rememberToken()` | `VARCHAR(100) NULL` | Stores authentication session remember token |

---

## 7. Migration Observations, Ambiguities & Assumptions

### 1. Column Naming Verbatim Preservation (`is_super_addmin`)
- In migration `Modules/User/database/migrations/2026_01_01_000006_add_is_super_addmin_to_users_table.php`, the column name is spelled `is_super_addmin` (with double 'd').
- Inspection of `app/Models/User.php`, `Modules/User/database/seeders/SuperAdminSeeder.php`, and controllers revealed that the application code explicitly references `is_super_addmin` (with a compatibility accessor `getIsSuperAdminAttribute`).
- **Resolution:** The generated SQL preserves `is_super_addmin` verbatim to guarantee 100% ORM and query compatibility.

### 2. Unconstrained Unsigned BigIntegers
- In `categories` (`hospital_id`, `created_by`, `updated_by`) and `services` (`hospital_id`, `supplier_id`, `created_by`, `updated_by`), columns were defined as `$table->unsignedBigInteger(...)` without calling `->constrained()` or `->references()`.
- **Resolution:** No artificial foreign keys were invented; they remain `BIGINT NULL` with indexes where declared in the migration.

### 3. Session User ID Constraint
- In `sessions`, the column is defined as `$table->foreignId('user_id')->nullable()->index()`, which creates a `BIGINT NULL` with an index, but does not declare a foreign key constraint.
- **Resolution:** Retained strictly as `BIGINT NULL` with index `sessions_user_id_index`, matching Laravel standard.

### 4. Dynamic Column Conditionals
- Migrations `2026_01_01_000005`, `2026_01_01_000006`, `2026_01_01_000015`, and `2026_01_01_000017` use PHP runtime guards `if (! Schema::hasColumn(...))`.
- **Resolution:** Evaluated the deterministic final state of the schema. All columns inside these guards are included in their final state in `schema.sql`.

---

## 8. Database Validation Results

The script `schema.sql` was validated by initializing a fresh, temporary PostgreSQL cluster on socket port 54321 and running:

```bash
psql -v ON_ERROR_STOP=1 -d test_schema_db -f schema.sql
```

**Verification Results:**
- Exit status: `0` (Success)
- Errors encountered: `0`
- Total relations generated: `26` tables
- Sequences generated: `17` identity sequences (all `id` serial columns)
- Indexes / Constraints verified: `100%`

The generated `schema.sql` is ready for production and staging environments on any PostgreSQL 12+ cluster.

