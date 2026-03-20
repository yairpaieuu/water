-- ============================================================
-- Water Purification Business CRM - Database Schema
-- Engine: InnoDB | Charset: utf8mb4_unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(150)     NOT NULL,
    `email`       VARCHAR(180)     NOT NULL,
    `password`    VARCHAR(255)     NOT NULL,
    `role`        ENUM('admin','manager','technician','sales') NOT NULL DEFAULT 'sales',
    `branch_id`   INT UNSIGNED     DEFAULT NULL,
    `status`      ENUM('active','inactive')                   NOT NULL DEFAULT 'active',
    `avatar`      VARCHAR(255)     DEFAULT NULL,
    `last_login`  DATETIME         DEFAULT NULL,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_branch_id` (`branch_id`),
    KEY `idx_users_role`      (`role`),
    KEY `idx_users_status`    (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- branches
-- (manager_id references employees; FK added after employees)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `branches`;
CREATE TABLE IF NOT EXISTS `branches` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(150)  NOT NULL,
    `address`     TEXT          DEFAULT NULL,
    `city`        VARCHAR(100)  DEFAULT NULL,
    `state`       VARCHAR(100)  DEFAULT NULL,
    `phone`       VARCHAR(30)   DEFAULT NULL,
    `email`       VARCHAR(180)  DEFAULT NULL,
    `manager_id`  INT UNSIGNED  DEFAULT NULL,
    `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_branches_status`     (`status`),
    KEY `idx_branches_manager_id` (`manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- customers
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `customer_code` VARCHAR(20)   NOT NULL,
    `first_name`    VARCHAR(100)  NOT NULL,
    `last_name`     VARCHAR(100)  NOT NULL,
    `email`         VARCHAR(180)  DEFAULT NULL,
    `phone`         VARCHAR(30)   NOT NULL,
    `alt_phone`     VARCHAR(30)   DEFAULT NULL,
    `address`       TEXT          DEFAULT NULL,
    `city`          VARCHAR(100)  DEFAULT NULL,
    `state`         VARCHAR(100)  DEFAULT NULL,
    `postal_code`   VARCHAR(20)   DEFAULT NULL,
    `branch_id`     INT UNSIGNED  DEFAULT NULL,
    `source`        ENUM('walk_in','referral','online','site_survey','lead_conversion') NOT NULL DEFAULT 'walk_in',
    `status`        ENUM('active','inactive','prospect')                                NOT NULL DEFAULT 'active',
    `notes`         TEXT          DEFAULT NULL,
    `created_by`    INT UNSIGNED  DEFAULT NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_customers_code` (`customer_code`),
    KEY `idx_customers_branch_id`  (`branch_id`),
    KEY `idx_customers_created_by` (`created_by`),
    KEY `idx_customers_status`     (`status`),
    KEY `idx_customers_email`      (`email`),
    KEY `idx_customers_phone`      (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- leads
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `leads`;
CREATE TABLE IF NOT EXISTS `leads` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `lead_code`   VARCHAR(20)   NOT NULL,
    `first_name`  VARCHAR(100)  NOT NULL,
    `last_name`   VARCHAR(100)  NOT NULL,
    `email`       VARCHAR(180)  DEFAULT NULL,
    `phone`       VARCHAR(30)   NOT NULL,
    `address`     TEXT          DEFAULT NULL,
    `city`        VARCHAR(100)  DEFAULT NULL,
    `branch_id`   INT UNSIGNED  DEFAULT NULL,
    `source`      ENUM('walk_in','referral','online','marketing','other') NOT NULL DEFAULT 'other',
    `status`      ENUM('new','contacted','qualified','proposal','won','lost') NOT NULL DEFAULT 'new',
    `notes`       TEXT          DEFAULT NULL,
    `assigned_to` INT UNSIGNED  DEFAULT NULL,
    `created_by`  INT UNSIGNED  DEFAULT NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_leads_code`        (`lead_code`),
    KEY `idx_leads_branch_id`         (`branch_id`),
    KEY `idx_leads_assigned_to`       (`assigned_to`),
    KEY `idx_leads_created_by`        (`created_by`),
    KEY `idx_leads_status`            (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- products
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
    `id`                     INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `product_code`           VARCHAR(30)     NOT NULL,
    `name`                   VARCHAR(200)    NOT NULL,
    `description`            TEXT            DEFAULT NULL,
    `category`               ENUM('purifier','accessory','chemical','spare_part') NOT NULL DEFAULT 'purifier',
    `brand`                  VARCHAR(100)    DEFAULT NULL,
    `model_number`           VARCHAR(100)    DEFAULT NULL,
    `purchase_price`         DECIMAL(10,2)   NOT NULL DEFAULT '0.00',
    `selling_price`          DECIMAL(10,2)   NOT NULL DEFAULT '0.00',
    `service_interval_months` INT            DEFAULT NULL,
    `status`                 ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`             DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`             DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_products_code`     (`product_code`),
    KEY `idx_products_category`       (`category`),
    KEY `idx_products_status`         (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- inventory_stock
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `inventory_stock`;
CREATE TABLE IF NOT EXISTS `inventory_stock` (
    `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `product_id`   INT UNSIGNED  NOT NULL,
    `branch_id`    INT UNSIGNED  NOT NULL,
    `quantity`     INT           NOT NULL DEFAULT 0,
    `min_quantity` INT           NOT NULL DEFAULT 0,
    `created_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_stock_product_branch` (`product_id`, `branch_id`),
    KEY `idx_stock_product_id` (`product_id`),
    KEY `idx_stock_branch_id`  (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- service_types
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `service_types`;
CREATE TABLE IF NOT EXISTS `service_types` (
    `id`              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `code`            VARCHAR(10)    NOT NULL,
    `name`            VARCHAR(100)   NOT NULL,
    `description`     TEXT           DEFAULT NULL,
    `interval_months` INT            NOT NULL DEFAULT 1,
    `category`        ENUM('domestic','commercial') NOT NULL DEFAULT 'domestic',
    `price`           DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_service_types_code` (`code`),
    KEY `idx_service_types_category`   (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- service_contracts
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `service_contracts`;
CREATE TABLE IF NOT EXISTS `service_contracts` (
    `id`                    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `contract_code`         VARCHAR(20)   NOT NULL,
    `customer_id`           INT UNSIGNED  NOT NULL,
    `product_id`            INT UNSIGNED  DEFAULT NULL,
    `service_type_id`       INT UNSIGNED  DEFAULT NULL,
    `branch_id`             INT UNSIGNED  DEFAULT NULL,
    `installation_date`     DATE          DEFAULT NULL,
    `next_service_date`     DATE          DEFAULT NULL,
    `status`                ENUM('active','expired','suspended','cancelled') NOT NULL DEFAULT 'active',
    `serial_number`         VARCHAR(100)  DEFAULT NULL,
    `location_notes`        TEXT          DEFAULT NULL,
    `assigned_technician_id` INT UNSIGNED DEFAULT NULL,
    `created_by`            INT UNSIGNED  DEFAULT NULL,
    `created_at`            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_contracts_code`               (`contract_code`),
    KEY `idx_contracts_customer_id`              (`customer_id`),
    KEY `idx_contracts_product_id`               (`product_id`),
    KEY `idx_contracts_service_type_id`          (`service_type_id`),
    KEY `idx_contracts_branch_id`                (`branch_id`),
    KEY `idx_contracts_assigned_technician_id`   (`assigned_technician_id`),
    KEY `idx_contracts_created_by`               (`created_by`),
    KEY `idx_contracts_next_service_date`        (`next_service_date`),
    KEY `idx_contracts_status`                   (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- service_jobs
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `service_jobs`;
CREATE TABLE IF NOT EXISTS `service_jobs` (
    `id`               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `job_code`         VARCHAR(20)    NOT NULL,
    `contract_id`      INT UNSIGNED   DEFAULT NULL,
    `customer_id`      INT UNSIGNED   NOT NULL,
    `branch_id`        INT UNSIGNED   DEFAULT NULL,
    `job_type`         ENUM('installation','maintenance','repair','survey') NOT NULL DEFAULT 'maintenance',
    `status`           ENUM('pending','assigned','in_progress','completed','cancelled')  NOT NULL DEFAULT 'pending',
    `priority`         ENUM('low','normal','high','urgent')                              NOT NULL DEFAULT 'normal',
    `scheduled_date`   DATE           DEFAULT NULL,
    `scheduled_time`   TIME           DEFAULT NULL,
    `assigned_to`      INT UNSIGNED   DEFAULT NULL,
    `notes`            TEXT           DEFAULT NULL,
    `completion_notes` TEXT           DEFAULT NULL,
    `parts_used`       JSON           DEFAULT NULL,
    `cost`             DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `created_by`       INT UNSIGNED   DEFAULT NULL,
    `created_at`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_jobs_code`         (`job_code`),
    KEY `idx_jobs_contract_id`        (`contract_id`),
    KEY `idx_jobs_customer_id`        (`customer_id`),
    KEY `idx_jobs_branch_id`          (`branch_id`),
    KEY `idx_jobs_assigned_to`        (`assigned_to`),
    KEY `idx_jobs_created_by`         (`created_by`),
    KEY `idx_jobs_status`             (`status`),
    KEY `idx_jobs_scheduled_date`     (`scheduled_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- sales
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
    `id`             INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `sale_code`      VARCHAR(20)    NOT NULL,
    `customer_id`    INT UNSIGNED   NOT NULL,
    `branch_id`      INT UNSIGNED   DEFAULT NULL,
    `sale_date`      DATE           NOT NULL,
    `status`         ENUM('quotation','confirmed','delivered','cancelled')   NOT NULL DEFAULT 'quotation',
    `payment_status` ENUM('pending','partial','paid')                        NOT NULL DEFAULT 'pending',
    `subtotal`       DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `discount`       DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `tax`            DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `total`          DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `paid_amount`    DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `notes`          TEXT           DEFAULT NULL,
    `created_by`     INT UNSIGNED   DEFAULT NULL,
    `created_at`     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_sales_code`      (`sale_code`),
    KEY `idx_sales_customer_id`     (`customer_id`),
    KEY `idx_sales_branch_id`       (`branch_id`),
    KEY `idx_sales_created_by`      (`created_by`),
    KEY `idx_sales_status`          (`status`),
    KEY `idx_sales_payment_status`  (`payment_status`),
    KEY `idx_sales_sale_date`       (`sale_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- sale_items
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE IF NOT EXISTS `sale_items` (
    `id`         INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `sale_id`    INT UNSIGNED   NOT NULL,
    `product_id` INT UNSIGNED   NOT NULL,
    `quantity`   INT            NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `discount`   DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `total`      DECIMAL(10,2)  NOT NULL DEFAULT '0.00',
    `created_at` DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_sale_items_sale_id`    (`sale_id`),
    KEY `idx_sale_items_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- employees
-- (must exist before branches.manager_id FK can be added)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
    `id`              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `employee_code`   VARCHAR(20)    NOT NULL,
    `first_name`      VARCHAR(100)   NOT NULL,
    `last_name`       VARCHAR(100)   NOT NULL,
    `email`           VARCHAR(180)   DEFAULT NULL,
    `phone`           VARCHAR(30)    DEFAULT NULL,
    `address`         TEXT           DEFAULT NULL,
    `city`            VARCHAR(100)   DEFAULT NULL,
    `branch_id`       INT UNSIGNED   DEFAULT NULL,
    `department`      ENUM('management','sales','technical','admin') NOT NULL DEFAULT 'admin',
    `position`        VARCHAR(150)   DEFAULT NULL,
    `date_joined`     DATE           DEFAULT NULL,
    `status`          ENUM('active','inactive','terminated') NOT NULL DEFAULT 'active',
    `user_id`         INT UNSIGNED   DEFAULT NULL,
    `salary`          DECIMAL(10,2)  DEFAULT NULL,
    `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_employees_code`    (`employee_code`),
    KEY `idx_employees_branch_id`     (`branch_id`),
    KEY `idx_employees_user_id`       (`user_id`),
    KEY `idx_employees_department`    (`department`),
    KEY `idx_employees_status`        (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- attendance
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `employee_id` INT UNSIGNED  NOT NULL,
    `date`        DATE          NOT NULL,
    `check_in`    TIME          DEFAULT NULL,
    `check_out`   TIME          DEFAULT NULL,
    `status`      ENUM('present','absent','late','half_day','leave') NOT NULL DEFAULT 'present',
    `notes`       TEXT          DEFAULT NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_attendance_emp_date` (`employee_id`, `date`),
    KEY `idx_attendance_employee_id`    (`employee_id`),
    KEY `idx_attendance_date`           (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- leave_requests
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE IF NOT EXISTS `leave_requests` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `employee_id` INT UNSIGNED  NOT NULL,
    `leave_type`  ENUM('annual','sick','casual','maternity','no_pay') NOT NULL DEFAULT 'annual',
    `start_date`  DATE          NOT NULL,
    `end_date`    DATE          NOT NULL,
    `days`        INT           NOT NULL DEFAULT 1,
    `reason`      TEXT          DEFAULT NULL,
    `status`      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `approved_by` INT UNSIGNED  DEFAULT NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_leave_employee_id` (`employee_id`),
    KEY `idx_leave_approved_by` (`approved_by`),
    KEY `idx_leave_status`      (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Foreign Key Constraints
-- ============================================================

-- users → branches
ALTER TABLE `users`
    ADD CONSTRAINT `fk_users_branch_id`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- branches → employees (manager)
ALTER TABLE `branches`
    ADD CONSTRAINT `fk_branches_manager_id`
        FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- customers → branches, users
ALTER TABLE `customers`
    ADD CONSTRAINT `fk_customers_branch_id`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_customers_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- leads → branches, users
ALTER TABLE `leads`
    ADD CONSTRAINT `fk_leads_branch_id`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_leads_assigned_to`
        FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_leads_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- inventory_stock → products, branches
ALTER TABLE `inventory_stock`
    ADD CONSTRAINT `fk_stock_product_id`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_stock_branch_id`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- service_contracts → customers, products, service_types, branches, users
ALTER TABLE `service_contracts`
    ADD CONSTRAINT `fk_contracts_customer_id`
        FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_contracts_product_id`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_contracts_service_type_id`
        FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_contracts_branch_id`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_contracts_assigned_technician_id`
        FOREIGN KEY (`assigned_technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_contracts_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- service_jobs → service_contracts, customers, branches, users
ALTER TABLE `service_jobs`
    ADD CONSTRAINT `fk_jobs_contract_id`
        FOREIGN KEY (`contract_id`) REFERENCES `service_contracts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_jobs_customer_id`
        FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_jobs_branch_id`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_jobs_assigned_to`
        FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_jobs_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- sales → customers, branches, users
ALTER TABLE `sales`
    ADD CONSTRAINT `fk_sales_customer_id`
        FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_sales_branch_id`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_sales_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- sale_items → sales, products
ALTER TABLE `sale_items`
    ADD CONSTRAINT `fk_sale_items_sale_id`
        FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_sale_items_product_id`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- employees → branches, users
ALTER TABLE `employees`
    ADD CONSTRAINT `fk_employees_branch_id`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_employees_user_id`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- attendance → employees
ALTER TABLE `attendance`
    ADD CONSTRAINT `fk_attendance_employee_id`
        FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- leave_requests → employees, users
ALTER TABLE `leave_requests`
    ADD CONSTRAINT `fk_leave_employee_id`
        FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_leave_approved_by`
        FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Seed Data
-- ============================================================

-- Admin user (password: Admin@1234)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`)
VALUES (
    'System Admin',
    'admin@aquacrm.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'active',
    NOW(),
    NOW()
);

-- Branches (manager_id left NULL until employees are created)
INSERT INTO `branches` (`name`, `address`, `city`, `state`, `phone`, `email`, `status`, `created_at`, `updated_at`)
VALUES
    ('Colombo HQ',    '123 Main Street, Colombo 03',  'Colombo', 'Western',  '+94 11 234 5678', 'colombo@aquacrm.com', 'active', NOW(), NOW()),
    ('Kandy Branch',  '45 Peradeniya Road, Kandy',    'Kandy',   'Central',  '+94 81 234 5678', 'kandy@aquacrm.com',   'active', NOW(), NOW()),
    ('Galle Branch',  '78 Galle Fort Road, Galle',    'Galle',   'Southern', '+94 91 234 5678', 'galle@aquacrm.com',   'active', NOW(), NOW());

-- Service types
INSERT INTO `service_types` (`code`, `name`, `description`, `interval_months`, `category`, `price`, `created_at`, `updated_at`)
VALUES
    ('D3M',   'Domestic 3 Month',  'Domestic service every 3 months',   3,  'domestic',   1500.00, NOW(), NOW()),
    ('D6M',   'Domestic 6 Month',  'Domestic service every 6 months',   6,  'domestic',   2500.00, NOW(), NOW()),
    ('D9M',   'Domestic 9 Month',  'Domestic service every 9 months',   9,  'domestic',   3200.00, NOW(), NOW()),
    ('D12M',  'Domestic 12 Month', 'Domestic service every 12 months',  12, 'domestic',   4000.00, NOW(), NOW()),
    ('S16M',  'S1 6 Month',        'S1 commercial service every 6 months',   6,  'commercial', 5000.00, NOW(), NOW()),
    ('S112M', 'S1 12 Month',       'S1 commercial service every 12 months',  12, 'commercial', 8500.00, NOW(), NOW()),
    ('S118M', 'S1 18 Month',       'S1 commercial service every 18 months',  18, 'commercial', 11000.00, NOW(), NOW()),
    ('S124M', 'S1 24 Month',       'S1 commercial service every 24 months',  24, 'commercial', 14000.00, NOW(), NOW());
