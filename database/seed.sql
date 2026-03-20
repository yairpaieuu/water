-- ============================================================
-- AquaCRM – Demo Seed Data
-- Run AFTER schema.sql has been executed.
--
-- This script:
--   1. Cleans all previous demo data (schema.sql's structural
--      seeds are preserved: admin user id=1, the three base
--      branches id=1/2/3, and settings).
--   2. Inserts a fresh, comprehensive dataset aligned with the
--      current machine-type and service-interval codes:
--
--        D3M   Domestic  3-month  |  D6M   Domestic  6-month
--        D9M   Domestic  9-month  |  D12M  Domestic 12-month
--        S16M  S1 Commercial  6-month
--        S112M S1 Commercial 12-month
--        S118M S1 Commercial 18-month
--        S124M S1 Commercial 24-month
--
-- Demo login credentials (all passwords = "Password1!"):
--   admin@aquacrm.test  (role: admin)
--   nuwan@aquacrm.test  (role: manager)
--   kamal@aquacrm.test  (role: technician)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. CLEAN – wipe all previous demo data
--    Preserves: schema.sql admin user (id=1), the three base
--    branches (id=1/2/3) and settings rows.
--
--    Using DELETE + AUTO_INCREMENT reset instead of TRUNCATE so
--    the script works on all MySQL 8.x and MariaDB versions.
--    TRUNCATE on InnoDB parent tables raises #1701 on some hosts
--    even when FOREIGN_KEY_CHECKS = 0 is set.  DELETE is always
--    safe once FK checks are disabled.
-- ============================================================

DELETE FROM `leave_requests`;
DELETE FROM `attendance`;
DELETE FROM `sale_items`;
DELETE FROM `sales`;
DELETE FROM `service_jobs`;
DELETE FROM `service_contracts`;
DELETE FROM `leads`;
DELETE FROM `inventory_stock`;
DELETE FROM `employees`;
DELETE FROM `customers`;
DELETE FROM `products`;
DELETE FROM `service_types`;

DELETE FROM `branches` WHERE `id` NOT IN (1, 2, 3);
DELETE FROM `users`    WHERE `id`  != 1;

-- Reset auto-increment counters so demo IDs start from 1
ALTER TABLE `leave_requests`    AUTO_INCREMENT = 1;
ALTER TABLE `attendance`        AUTO_INCREMENT = 1;
ALTER TABLE `sale_items`        AUTO_INCREMENT = 1;
ALTER TABLE `sales`             AUTO_INCREMENT = 1;
ALTER TABLE `service_jobs`      AUTO_INCREMENT = 1;
ALTER TABLE `service_contracts` AUTO_INCREMENT = 1;
ALTER TABLE `leads`             AUTO_INCREMENT = 1;
ALTER TABLE `inventory_stock`   AUTO_INCREMENT = 1;
ALTER TABLE `employees`         AUTO_INCREMENT = 1;
ALTER TABLE `customers`         AUTO_INCREMENT = 1;
ALTER TABLE `products`          AUTO_INCREMENT = 1;
ALTER TABLE `service_types`     AUTO_INCREMENT = 1;
ALTER TABLE `branches`          AUTO_INCREMENT = 4;
ALTER TABLE `users`             AUTO_INCREMENT = 2;

-- ============================================================
-- 2. BRANCHES
--    IDs 1-3 already exist from schema.sql (Colombo HQ, Kandy,
--    Galle).  This insert adds two more branches.
-- ============================================================

INSERT IGNORE INTO `branches`
    (`id`, `name`, `address`, `city`, `state`, `phone`, `email`, `status`)
VALUES
    (4, 'Matara Branch',  '45 Beach Road, Matara',    'Matara',  'Southern', '+94 41 222 3456', 'matara@aquacrm.test',  'active'),
    (5, 'Negombo Branch', '18 Colombo Road, Negombo', 'Negombo', 'Western',  '+94 31 222 4567', 'negombo@aquacrm.test', 'active');

-- ============================================================
-- 3. USERS
--    ID 1 (admin) is already created by schema.sql.
--    Password hash = bcrypt("Password1!") for all demo users.
--    Valid roles: admin | manager | technician | sales
-- ============================================================

INSERT IGNORE INTO `users`
    (`id`, `name`, `username`, `email`, `password`, `role`, `branch_id`, `status`)
VALUES
    (2, 'Nuwan Perera',       'nuwan.perera',       'nuwan@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',    1, 'active'),
    (3, 'Kamal Silva',        'kamal.silva',        'kamal@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician', 1, 'active'),
    (4, 'Roshani Fernando',   'roshani.fernando',   'roshani@aquacrm.test',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',    2, 'active'),
    (5, 'Asanka Jayawardena', 'asanka.jayawardena', 'asanka@aquacrm.test',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician', 2, 'active'),
    (6, 'Nirosha Wijesinghe', 'nirosha.wijesinghe', 'nirosha@aquacrm.test',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',    3, 'active'),
    (7, 'Pradeep Kumara',     'pradeep.kumara',     'pradeep@aquacrm.test',  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician', 3, 'active'),
    (8, 'Mahesh Rathnayake',  'mahesh.rathnayake',  'mahesh@aquacrm.test',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales',      4, 'active');

-- ============================================================
-- 4. PRODUCTS
--    PRD-001 Domestic Water Purifier  → paired with D-series
--    PRD-002 S1 Commercial Purifier   → paired with S1-series
--    PRD-003/004/005 spare parts / chemicals (no service type)
--    Prices are in LKR.
-- ============================================================

INSERT INTO `products`
    (`id`, `product_code`, `name`, `description`, `category`, `brand`, `model_number`,
     `purchase_price`, `selling_price`, `service_interval_months`, `status`)
VALUES
    (1, 'PRD-001', 'Domestic Water Purifier',
        'Domestic RO purifier for residential use (D-series)',
        'purifier',   'AquaPure',  'D-SERIES',   8500.00, 15000.00,  6, 'active'),
    (2, 'PRD-002', 'S1 Commercial Water Purifier',
        'S1-series RO purifier for semi-commercial and commercial use',
        'purifier',   'AquaPure',  'S1-SERIES', 12000.00, 22000.00, 12, 'active'),
    (3, 'PRD-003', 'Membrane Filter 50GPD',
        'Replacement RO membrane, 50 gallons per day',
        'spare_part', 'FilterMax', 'MEM-50GPD',  1800.00,  3500.00, NULL, 'active'),
    (4, 'PRD-004', 'Pre-Filter Set (3-pack)',
        'Sediment + carbon block + carbon granule set',
        'spare_part', 'FilterMax', 'PRE-3P',     1200.00,  2200.00, NULL, 'active'),
    (5, 'PRD-005', 'Anti-Scale Chemical 5L',
        'Liquid anti-scale treatment for water purification systems',
        'chemical',   'ChemClear', 'AS-5L',       800.00,  1800.00, NULL, 'active');

-- ============================================================
-- 5. INVENTORY STOCK  (5 products × 5 branches = 25 rows)
-- ============================================================

INSERT INTO `inventory_stock` (`product_id`, `branch_id`, `quantity`, `min_quantity`) VALUES
-- Branch 1 – Colombo HQ
(1, 1, 12, 3), (2, 1,  6, 2), (3, 1, 40, 10), (4, 1, 25, 5), (5, 1, 20, 5),
-- Branch 2 – Kandy
(1, 2,  8, 2), (2, 2,  4,  2), (3, 2, 30,  8), (4, 2, 20, 5), (5, 2, 15, 5),
-- Branch 3 – Galle
(1, 3,  6, 2), (2, 3,  3,  1), (3, 3, 22,  6), (4, 3, 15, 4), (5, 3, 10, 3),
-- Branch 4 – Matara
(1, 4,  9, 2), (2, 4,  5,  1), (3, 4, 35,  8), (4, 4, 22, 5), (5, 4, 18, 4),
-- Branch 5 – Negombo
(1, 5,  4, 2), (2, 5,  2,  1), (3, 5, 25,  7), (4, 5, 14, 4), (5, 5,  8, 3);

-- ============================================================
-- 6. SERVICE TYPES
--    After the DELETE above these must be re-inserted.
--    IDs match schema.sql's auto-assigned order so that a
--    fresh schema.sql + seed.sql run is fully consistent.
-- ============================================================

INSERT INTO `service_types`
    (`id`, `code`, `name`, `description`, `interval_months`, `category`, `price`)
VALUES
    (1, 'D3M',   'Domestic 3 Month',   'Domestic service every 3 months',          3,  'domestic',    1500.00),
    (2, 'D6M',   'Domestic 6 Month',   'Domestic service every 6 months',          6,  'domestic',    2500.00),
    (3, 'D9M',   'Domestic 9 Month',   'Domestic service every 9 months',          9,  'domestic',    3200.00),
    (4, 'D12M',  'Domestic 12 Month',  'Domestic service every 12 months',        12,  'domestic',    4000.00),
    (5, 'S16M',  'S1 6 Month',         'S1 commercial service every 6 months',     6,  'commercial',  5000.00),
    (6, 'S112M', 'S1 12 Month',        'S1 commercial service every 12 months',   12,  'commercial',  8500.00),
    (7, 'S118M', 'S1 18 Month',        'S1 commercial service every 18 months',   18,  'commercial', 11000.00),
    (8, 'S124M', 'S1 24 Month',        'S1 commercial service every 24 months',   24,  'commercial', 14000.00);

-- ============================================================
-- 7. EMPLOYEES
--    user_id links to users table; branch managers are set
--    via UPDATE after all employees are inserted.
-- ============================================================

INSERT INTO `employees`
    (`id`, `employee_code`, `first_name`, `last_name`, `email`, `phone`,
     `city`, `branch_id`, `department`, `position`, `date_joined`, `status`, `salary`, `user_id`)
VALUES
    (1, 'EMP-001', 'Nuwan',    'Perera',       'nuwan@aquacrm.test',    '+94 77 100 1001',
        'Colombo', 1, 'management', 'Branch Manager',    '2019-03-01', 'active', 85000.00, 2),
    (2, 'EMP-002', 'Kamal',    'Silva',        'kamal@aquacrm.test',    '+94 77 100 1002',
        'Colombo', 1, 'technical',  'Senior Technician', '2019-07-15', 'active', 62000.00, 3),
    (3, 'EMP-003', 'Roshani',  'Fernando',     'roshani@aquacrm.test',  '+94 77 100 1003',
        'Kandy',   2, 'management', 'Branch Manager',    '2020-02-10', 'active', 82000.00, 4),
    (4, 'EMP-004', 'Asanka',   'Jayawardena',  'asanka@aquacrm.test',   '+94 77 100 1004',
        'Kandy',   2, 'technical',  'Technician',        '2020-09-20', 'active', 56000.00, 5),
    (5, 'EMP-005', 'Nirosha',  'Wijesinghe',   'nirosha@aquacrm.test',  '+94 77 100 1005',
        'Galle',   3, 'management', 'Branch Manager',    '2020-01-05', 'active', 82000.00, 6),
    (6, 'EMP-006', 'Pradeep',  'Kumara',       'pradeep@aquacrm.test',  '+94 77 100 1006',
        'Galle',   3, 'technical',  'Technician',        '2021-04-01', 'active', 54000.00, 7),
    (7, 'EMP-007', 'Mahesh',   'Rathnayake',   'mahesh@aquacrm.test',   '+94 77 100 1007',
        'Matara',  4, 'sales',      'Sales Executive',   '2021-11-15', 'active', 52000.00, 8),
    (8, 'EMP-008', 'Dilani',   'Kumarasinghe', 'dilani@aquacrm.test',   '+94 77 100 1008',
        'Colombo', 1, 'admin',      'Admin Officer',     '2022-06-01', 'active', 48000.00, NULL);

-- Set branch managers (JOIN ensures silently skipped if employee absent)
UPDATE `branches` b JOIN `employees` e ON e.`id` = 1 SET b.`manager_id` = 1 WHERE b.`id` = 1;
UPDATE `branches` b JOIN `employees` e ON e.`id` = 3 SET b.`manager_id` = 3 WHERE b.`id` = 2;
UPDATE `branches` b JOIN `employees` e ON e.`id` = 5 SET b.`manager_id` = 5 WHERE b.`id` = 3;

-- ============================================================
-- 8. CUSTOMERS
--    CUST-0001 to CUST-0010: domestic customers
--    CUST-0011 to CUST-0012: commercial / S1 customers
-- ============================================================

INSERT INTO `customers`
    (`id`, `customer_code`, `first_name`, `last_name`, `email`, `phone`,
     `address`, `city`, `state`, `postal_code`,
     `branch_id`, `source`, `status`, `created_by`)
VALUES
    (1,  'CUST-0001', 'Chaminda',  'Perera',        'chaminda.perera@example.com',    '+94 71 201 0001',
         '14/A, Galle Road, Colombo 03',      'Colombo',  'Western',  '00300', 1, 'referral',       'active', 1),
    (2,  'CUST-0002', 'Malini',    'De Silva',       'malini.desilva@example.com',     '+94 71 201 0002',
         '23, Duplication Road, Colombo 04',  'Colombo',  'Western',  '00400', 1, 'online',          'active', 1),
    (3,  'CUST-0003', 'Ruwan',     'Bandara',        'ruwan.bandara@example.com',      '+94 71 201 0003',
         '7, Temple Road, Kandy',             'Kandy',    'Central',  '20000', 2, 'walk_in',         'active', 1),
    (4,  'CUST-0004', 'Priyanka',  'Gunawardena',    'priyanka.guna@example.com',      '+94 71 201 0004',
         '45, Peradeniya Road, Kandy',        'Kandy',    'Central',  '20100', 2, 'lead_conversion', 'active', 1),
    (5,  'CUST-0005', 'Saman',     'Wickramasinghe', 'saman.wickrama@example.com',     '+94 71 201 0005',
         '8, Fort Road, Galle',               'Galle',    'Southern', '80000', 3, 'site_survey',     'active', 1),
    (6,  'CUST-0006', 'Dilhara',   'Kumari',         'dilhara.kumari@example.com',     '+94 71 201 0006',
         '15, Wakwella Road, Galle',          'Galle',    'Southern', '80010', 3, 'referral',        'active', 1),
    (7,  'CUST-0007', 'Nimal',     'Jayasekara',     'nimal.jayasekara@example.com',   '+94 71 201 0007',
         '3, Matara Road, Matara',            'Matara',   'Southern', '81000', 4, 'walk_in',         'active', 1),
    (8,  'CUST-0008', 'Sandya',    'Pathirana',      'sandya.pathirana@example.com',   '+94 71 201 0008',
         '21, New Road, Matara',              'Matara',   'Southern', '81100', 4, 'online',          'active', 1),
    (9,  'CUST-0009', 'Roshan',    'Senanayake',     'roshan.senanayake@example.com',  '+94 71 201 0009',
         '9, Lewis Place, Negombo',           'Negombo',  'Western',  '11500', 5, 'referral',        'active', 1),
    (10, 'CUST-0010', 'Kumari',    'Amarasinghe',    'kumari.amarasinghe@example.com', '+94 71 201 0010',
         '32, Colombo Road, Negombo',         'Negombo',  'Western',  '11500', 5, 'walk_in',         'active', 1),
    -- Commercial / S1 customers
    (11, 'CUST-0011', 'Lanka',     'Hotels Ltd',     'info@lankahotels.example.com',   '+94 11 201 0011',
         '120, Union Place, Colombo 02',      'Colombo',  'Western',  '00200', 1, 'site_survey',     'active', 1),
    (12, 'CUST-0012', 'Kandy',     'Spice Garden',   'info@kandyspice.example.com',    '+94 81 201 0012',
         '88, Peradeniya Road, Kandy',        'Kandy',    'Central',  '20000', 2, 'referral',        'active', 1);

-- ============================================================
-- 9. LEADS
-- ============================================================

INSERT INTO `leads`
    (`id`, `lead_code`, `first_name`, `last_name`, `email`, `phone`,
     `address`, `city`, `branch_id`, `source`, `status`, `assigned_to`, `created_by`)
VALUES
    (1, 'LEAD-0001', 'Tharaka',   'Liyanage',    'tharaka.liyanage@example.com',  '+94 71 301 0001',
        '55, High Level Road, Colombo 05', 'Colombo', 1, 'online',    'new',       2, 1),
    (2, 'LEAD-0002', 'Nilanthi',  'Mendis',      'nilanthi.mendis@example.com',   '+94 71 301 0002',
        '12, Dharmarama Road, Kandy',      'Kandy',   2, 'referral',  'contacted', 4, 1),
    (3, 'LEAD-0003', 'Buddhika',  'Rathnasiri',  'buddhika.rath@example.com',     '+94 71 301 0003',
        '30, Marine Drive, Galle',         'Galle',   3, 'marketing', 'qualified', 6, 1),
    (4, 'LEAD-0004', 'Isuru',     'Ekanayake',   'isuru.ekanayake@example.com',   '+94 71 301 0004',
        '7, Main Street, Matara',          'Matara',  4, 'walk_in',   'proposal',  2, 1),
    (5, 'LEAD-0005', 'Shiranthi', 'Dissanayake', 'shiranthi.dis@example.com',     '+94 71 301 0005',
        '44, Old Road, Negombo',           'Negombo', 5, 'other',     'won',       2, 1),
    (6, 'LEAD-0006', 'Chamath',   'Peiris',      'chamath.peiris@example.com',    '+94 71 301 0006',
        '19, Ward Place, Colombo 07',      'Colombo', 1, 'online',    'new',       4, 1);

-- ============================================================
-- 10. SERVICE CONTRACTS
--     Domestic customers (1-10) → PRD-001 + D-series types (1-4)
--     Commercial customers (11-12) → PRD-002 + S1-series types (5-8)
--     assigned_technician_id references users.id (technician role)
--     Contracts 6-12 use relative dates so dashboard widgets
--     ("Services Due This Week", "Upcoming Services") stay live.
-- ============================================================

INSERT INTO `service_contracts`
    (`id`, `contract_code`, `customer_id`, `product_id`, `service_type_id`,
     `branch_id`, `installation_date`, `next_service_date`,
     `status`, `serial_number`, `assigned_technician_id`, `created_by`)
VALUES
    -- ---- D-series domestic contracts (fixed historical dates) ----
    (1,  'CON-0001', 1,  1, 2, 1,                      -- D6M – Chaminda Perera
         '2024-09-15', '2025-03-15', 'active', 'SN-DOM-10001', 3, 1),
    (2,  'CON-0002', 2,  1, 1, 1,                      -- D3M – Malini De Silva
         '2024-12-01', '2025-03-01', 'active', 'SN-DOM-10002', 3, 1),
    (3,  'CON-0003', 3,  1, 3, 2,                      -- D9M – Ruwan Bandara
         '2024-06-10', '2025-03-10', 'active', 'SN-DOM-10003', 5, 1),
    (4,  'CON-0004', 4,  1, 4, 2,                      -- D12M – Priyanka Gunawardena
         '2024-03-05', '2025-03-05', 'active', 'SN-DOM-10004', 5, 1),
    (5,  'CON-0005', 5,  1, 2, 3,                      -- D6M – Saman Wickramasinghe
         '2024-08-20', '2025-02-20', 'active', 'SN-DOM-10005', 7, 1),
    -- ---- D-series domestic contracts (relative dates for dashboard) ----
    (6,  'CON-0006', 6,  1, 1, 3,                      -- D3M – Dilhara Kumari (due +2 days)
         DATE_SUB(CURDATE(), INTERVAL 88  DAY), DATE_ADD(CURDATE(), INTERVAL 2  DAY), 'active', 'SN-DOM-10006', 7, 1),
    (7,  'CON-0007', 7,  1, 3, 4,                      -- D9M – Nimal Jayasekara (due +5 days)
         DATE_SUB(CURDATE(), INTERVAL 265 DAY), DATE_ADD(CURDATE(), INTERVAL 5  DAY), 'active', 'SN-DOM-10007', 3, 1),
    (8,  'CON-0008', 8,  1, 4, 4,                      -- D12M – Sandya Pathirana (due today)
         DATE_SUB(CURDATE(), INTERVAL 365 DAY), CURDATE(),                            'active', 'SN-DOM-10008', 3, 1),
    (9,  'CON-0009', 9,  1, 2, 5,                      -- D6M – Roshan Senanayake (due +3 days)
         DATE_SUB(CURDATE(), INTERVAL 177 DAY), DATE_ADD(CURDATE(), INTERVAL 3  DAY), 'active', 'SN-DOM-10009', 3, 1),
    (10, 'CON-0010', 10, 1, 1, 5,                      -- D3M – Kumari Amarasinghe (due next month)
         DATE_SUB(CURDATE(), INTERVAL 60  DAY), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'active', 'SN-DOM-10010', 5, 1),
    -- ---- S1-series commercial contracts ----
    (11, 'CON-0011', 11, 2, 5, 1,                      -- S16M – Lanka Hotels Ltd (due +1 day)
         DATE_SUB(CURDATE(), INTERVAL 179 DAY), DATE_ADD(CURDATE(), INTERVAL 1  DAY), 'active', 'SN-S1C-10011', 3, 1),
    (12, 'CON-0012', 12, 2, 6, 2,                      -- S112M – Kandy Spice Garden (due 9 months out)
         DATE_SUB(CURDATE(), INTERVAL 90  DAY), DATE_ADD(CURDATE(), INTERVAL 9  MONTH), 'active', 'SN-S1C-10012', 5, 1);

-- ============================================================
-- 11. SERVICE JOBS
--     Jobs 1-5: historical (completed) – explicit past created_at
--     Jobs 6-10: upcoming this week – created_at = NOW() so they
--       surface in "Recent Jobs" and "Services Due This Week"
-- ============================================================

INSERT INTO `service_jobs`
    (`id`, `job_code`, `contract_id`, `customer_id`, `branch_id`, `job_type`,
     `status`, `priority`, `scheduled_date`, `scheduled_time`,
     `assigned_to`, `notes`, `cost`, `created_by`, `created_at`)
VALUES
    -- ---- Historical completed jobs ----
    (1,  'JOB-0001', 1,  1,  1, 'installation', 'completed', 'normal',
         '2024-09-15', '09:00:00', 3,
         'Domestic purifier (D6M) installed, full system test passed.',
         15000.00, 1, '2024-09-14 10:00:00'),
    (2,  'JOB-0002', 1,  1,  1, 'maintenance',  'completed', 'normal',
         '2025-03-15', '09:00:00', 3,
         'D6M 6-month maintenance: membrane check, filter flush, water quality test.',
         2500.00,  1, '2025-03-12 09:00:00'),
    (3,  'JOB-0003', 5,  5,  3, 'maintenance',  'completed', 'high',
         '2025-02-20', '08:30:00', 7,
         'D6M domestic service completed – filters replaced, system flushed and tested.',
         2500.00,  1, '2025-02-18 10:00:00'),
    (4,  'JOB-0004', 11, 11, 1, 'installation', 'completed', 'high',
         DATE_SUB(CURDATE(), INTERVAL 179 DAY), '10:00:00', 3,
         'S16M S1 commercial unit installed at Lanka Hotels Ltd – all outlets tested.',
         22000.00, 1, DATE_SUB(NOW(), INTERVAL 180 DAY)),
    (5,  'JOB-0005', 12, 12, 2, 'installation', 'completed', 'normal',
         DATE_SUB(CURDATE(), INTERVAL 89 DAY), '09:30:00', 5,
         'S112M S1 commercial unit installed at Kandy Spice Garden.',
         22000.00, 1, DATE_SUB(NOW(), INTERVAL 90 DAY)),
    -- ---- Upcoming jobs (this week) – for "Services Due This Week" and "Recent Jobs" ----
    (6,  'JOB-0006', 8,  8,  4, 'maintenance',  'pending',  'high',
         CURDATE(),                           '09:00:00', 3,
         'D12M annual maintenance due today – full system inspection for Sandya Pathirana.',
         4000.00,  1, NOW()),
    (7,  'JOB-0007', 11, 11, 1, 'maintenance',  'assigned', 'normal',
         DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', 3,
         'S16M 6-month commercial service at Lanka Hotels Ltd – deep clean and filter check.',
         5000.00,  1, NOW()),
    (8,  'JOB-0008', 6,  6,  3, 'maintenance',  'assigned', 'normal',
         DATE_ADD(CURDATE(), INTERVAL 2 DAY), '11:00:00', 7,
         'D3M 3-month filter flush for Dilhara Kumari – Galle branch.',
         1500.00,  1, NOW()),
    (9,  'JOB-0009', 9,  9,  5, 'maintenance',  'pending',  'normal',
         DATE_ADD(CURDATE(), INTERVAL 3 DAY), '09:00:00', 3,
         'D6M 6-month domestic service for Roshan Senanayake – Negombo branch.',
         2500.00,  1, NOW()),
    (10, 'JOB-0010', 7,  7,  4, 'maintenance',  'pending',  'normal',
         DATE_ADD(CURDATE(), INTERVAL 5 DAY), '14:00:00', 3,
         'D9M 9-month service for Nimal Jayasekara – Matara branch.',
         3200.00,  1, NOW());

-- ============================================================
-- 12. SALES
--     Spread over last 12 months for the revenue chart.
--     Recent sales (ids 12-15) use CURDATE() relative dates
--     so "Recent Sales" and "Monthly Revenue" KPIs are always
--     populated.
--     Prices in LKR.
-- ============================================================

INSERT INTO `sales`
    (`id`, `sale_code`, `customer_id`, `branch_id`, `sale_date`,
     `status`, `payment_status`, `subtotal`, `discount`, `tax`, `total`, `paid_amount`,
     `notes`, `created_by`, `created_at`)
VALUES
    (1,  'SALE-0001', 1,  1, DATE_SUB(CURDATE(), INTERVAL 11 MONTH),
         'delivered', 'paid',    15000.00,    0.00, 0.00, 15000.00, 15000.00,
         'Domestic Water Purifier – Chaminda Perera',          1, DATE_SUB(NOW(), INTERVAL 11 MONTH)),
    (2,  'SALE-0002', 11, 1, DATE_SUB(CURDATE(), INTERVAL 10 MONTH),
         'delivered', 'paid',    22000.00,    0.00, 0.00, 22000.00, 22000.00,
         'S1 Commercial Water Purifier – Lanka Hotels Ltd',     1, DATE_SUB(NOW(), INTERVAL 10 MONTH)),
    (3,  'SALE-0003', 3,  2, DATE_SUB(CURDATE(), INTERVAL  9 MONTH),
         'delivered', 'paid',    15000.00,    0.00, 0.00, 15000.00, 15000.00,
         'Domestic Water Purifier – Ruwan Bandara',             1, DATE_SUB(NOW(), INTERVAL  9 MONTH)),
    (4,  'SALE-0004', 5,  3, DATE_SUB(CURDATE(), INTERVAL  8 MONTH),
         'delivered', 'paid',    15000.00,    0.00, 0.00, 15000.00, 15000.00,
         'Domestic Water Purifier – Saman Wickramasinghe',      1, DATE_SUB(NOW(), INTERVAL  8 MONTH)),
    (5,  'SALE-0005', 12, 2, DATE_SUB(CURDATE(), INTERVAL  7 MONTH),
         'delivered', 'paid',    22000.00, 1000.00, 0.00, 21000.00, 21000.00,
         'S1 Commercial Water Purifier – Kandy Spice Garden',   1, DATE_SUB(NOW(), INTERVAL  7 MONTH)),
    (6,  'SALE-0006', 7,  4, DATE_SUB(CURDATE(), INTERVAL  6 MONTH),
         'delivered', 'paid',    15000.00,    0.00, 0.00, 15000.00, 15000.00,
         'Domestic Water Purifier – Nimal Jayasekara',          1, DATE_SUB(NOW(), INTERVAL  6 MONTH)),
    (7,  'SALE-0007', 2,  1, DATE_SUB(CURDATE(), INTERVAL  5 MONTH),
         'delivered', 'paid',    15000.00,    0.00, 0.00, 15000.00, 15000.00,
         'Domestic Water Purifier – Malini De Silva',            1, DATE_SUB(NOW(), INTERVAL  5 MONTH)),
    (8,  'SALE-0008', 4,  2, DATE_SUB(CURDATE(), INTERVAL  4 MONTH),
         'delivered', 'paid',    15000.00,  500.00, 0.00, 14500.00, 14500.00,
         'Domestic Water Purifier – Priyanka Gunawardena',      1, DATE_SUB(NOW(), INTERVAL  4 MONTH)),
    (9,  'SALE-0009', 6,  3, DATE_SUB(CURDATE(), INTERVAL  3 MONTH),
         'delivered', 'paid',    15000.00,    0.00, 0.00, 15000.00, 15000.00,
         'Domestic Water Purifier – Dilhara Kumari',            1, DATE_SUB(NOW(), INTERVAL  3 MONTH)),
    (10, 'SALE-0010', 8,  4, DATE_SUB(CURDATE(), INTERVAL  2 MONTH),
         'delivered', 'paid',    15000.00,    0.00, 0.00, 15000.00, 15000.00,
         'Domestic Water Purifier – Sandya Pathirana',          1, DATE_SUB(NOW(), INTERVAL  2 MONTH)),
    (11, 'SALE-0011', 9,  5, DATE_SUB(CURDATE(), INTERVAL  1 MONTH),
         'delivered', 'paid',    15000.00,    0.00, 0.00, 15000.00, 15000.00,
         'Domestic Water Purifier – Roshan Senanayake',         1, DATE_SUB(NOW(), INTERVAL  1 MONTH)),
    -- Recent sales (for "Recent Sales" list and "Monthly Revenue" KPI)
    (12, 'SALE-0012', 1,  1, DATE_SUB(CURDATE(), INTERVAL  3 DAY),
         'delivered', 'paid',    3500.00,    0.00, 0.00,  3500.00,  3500.00,
         'Membrane filter replacement – Chaminda Perera',       1, DATE_SUB(NOW(), INTERVAL  3 DAY)),
    (13, 'SALE-0013', 3,  2, DATE_SUB(CURDATE(), INTERVAL  2 DAY),
         'confirmed', 'partial', 2200.00,  200.00, 0.00,  2000.00,  1000.00,
         'Pre-filter set – Ruwan Bandara (partial payment)',     1, DATE_SUB(NOW(), INTERVAL  2 DAY)),
    (14, 'SALE-0014', 11, 1, DATE_SUB(CURDATE(), INTERVAL  1 DAY),
         'delivered', 'paid',    5300.00,    0.00, 0.00,  5300.00,  5300.00,
         'Membrane + anti-scale chemical – Lanka Hotels Ltd',   1, DATE_SUB(NOW(), INTERVAL  1 DAY)),
    (15, 'SALE-0015', 5,  3, CURDATE(),
         'quotation', 'pending', 3500.00,    0.00, 0.00,  3500.00,     0.00,
         'Membrane filter quotation – Saman Wickramasinghe',    1, NOW());

-- ============================================================
-- 13. SALE ITEMS
-- ============================================================

INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `discount`, `total`) VALUES
-- Unit sales (purifiers)
(1,  1, 1, 15000.00,    0.00, 15000.00),  -- PRD-001
(2,  2, 1, 22000.00,    0.00, 22000.00),  -- PRD-002
(3,  1, 1, 15000.00,    0.00, 15000.00),
(4,  1, 1, 15000.00,    0.00, 15000.00),
(5,  2, 1, 22000.00, 1000.00, 21000.00),
(6,  1, 1, 15000.00,    0.00, 15000.00),
(7,  1, 1, 15000.00,    0.00, 15000.00),
(8,  1, 1, 15000.00,  500.00, 14500.00),
(9,  1, 1, 15000.00,    0.00, 15000.00),
(10, 1, 1, 15000.00,    0.00, 15000.00),
(11, 1, 1, 15000.00,    0.00, 15000.00),
-- Spare parts / chemicals
(12, 3, 1,  3500.00,    0.00,  3500.00),  -- Membrane filter
(13, 4, 1,  2200.00,  200.00,  2000.00),  -- Pre-filter set
(14, 3, 1,  3500.00,    0.00,  3500.00),  -- Membrane filter
(14, 5, 1,  1800.00,    0.00,  1800.00),  -- Anti-scale chemical
(15, 3, 1,  3500.00,    0.00,  3500.00);  -- Membrane filter (quotation)

-- ============================================================
-- 14. ATTENDANCE  (last 5 working days × 8 employees = 40 rows)
-- ============================================================

INSERT INTO `attendance` (`employee_id`, `date`, `check_in`, `check_out`, `status`) VALUES
-- EMP-001 Nuwan Perera
(1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:55:00', '17:05:00', 'present'),
(1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:10:00', '17:00:00', 'late'),
(1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '08:50:00', '17:00:00', 'present'),
(1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:00:00', '17:00:00', 'present'),
(1, CURDATE(),                           '09:05:00', '17:00:00', 'present'),
-- EMP-002 Kamal Silva
(2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00', 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:15:00', '17:00:00', 'late'),
(2, CURDATE(),                           '09:00:00', '17:00:00', 'present'),
-- EMP-003 Roshani Fernando
(3, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:45:00', '17:15:00', 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00', 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), NULL,        NULL,       'absent'),
(3, CURDATE(),                           '08:30:00', '17:00:00', 'present'),
-- EMP-004 Asanka Jayawardena
(4, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(4, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(4, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:30:00', '17:00:00', 'late'),
(4, DATE_SUB(CURDATE(), INTERVAL 1 DAY), NULL,        NULL,       'absent'),
(4, CURDATE(),                           '09:00:00', '17:00:00', 'present'),
-- EMP-005 Nirosha Wijesinghe
(5, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:30:00', '17:00:00', 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '08:30:00', '17:00:00', 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '08:45:00', '17:00:00', 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:00:00', '17:00:00', 'present'),
(5, CURDATE(),                           '08:30:00', '17:00:00', 'present'),
-- EMP-006 Pradeep Kumara
(6, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(6, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(6, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00', 'present'),
(6, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:10:00', '17:00:00', 'late'),
(6, CURDATE(),                           '09:00:00', '17:00:00', 'present'),
-- EMP-007 Mahesh Rathnayake
(7, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:45:00', '17:00:00', 'present'),
(7, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '08:45:00', '17:00:00', 'present'),
(7, DATE_SUB(CURDATE(), INTERVAL 2 DAY), NULL,        NULL,       'absent'),
(7, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:45:00', '17:00:00', 'present'),
(7, CURDATE(),                           '08:45:00', '17:00:00', 'present'),
-- EMP-008 Dilani Kumarasinghe
(8, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(8, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(8, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00', 'present'),
(8, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:00:00', '17:00:00', 'present'),
(8, CURDATE(),                           '09:00:00', '17:00:00', 'present');

-- ============================================================
-- 15. LEAVE REQUESTS
-- ============================================================

INSERT INTO `leave_requests`
    (`employee_id`, `leave_type`, `start_date`, `end_date`, `days`, `reason`, `status`, `approved_by`)
VALUES
    (2, 'annual',    '2025-08-04', '2025-08-06', 3, 'Family vacation',          'approved', 1),
    (3, 'sick',      '2025-07-21', '2025-07-21', 1, 'Fever and rest',           'approved', 1),
    (4, 'casual',    '2025-09-10', '2025-09-10', 1, 'Personal errand',          'pending',  NULL),
    (5, 'annual',    '2025-10-01', '2025-10-03', 3, 'Annual leave',             'pending',  NULL),
    (1, 'maternity', '2026-01-15', '2026-04-15',60, 'Maternity leave',          'approved', 1),
    (6, 'annual',    '2025-12-20', '2025-12-24', 5, 'Year-end holiday',         'approved', 1),
    (7, 'sick',      '2025-11-03', '2025-11-03', 1, 'Not feeling well',         'approved', 1),
    (8, 'casual',    '2026-02-14', '2026-02-14', 1, 'Personal errand',          'pending',  NULL),
    -- no_pay leave type (covers all five leave_type ENUM values, employee_id=2)
    (2, 'no_pay',    '2025-06-02', '2025-06-06', 5, 'Unpaid personal leave',    'approved', 1);

-- ============================================================
-- 16. ADDITIONAL DATA FOR COMPLETE PROCESS COVERAGE
--     The sections below ensure every ENUM value, every
--     application feature, and every report query has realistic
--     data to display.
-- ============================================================

-- ------------------------------------------------------------
-- 16a. Settings – populate all configurable keys so the
--      Settings page is fully populated on first load.
--      INSERT IGNORE means schema.sql base values are preserved.
-- ------------------------------------------------------------
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
    ('app_name',    'AquaCRM'),
    ('timezone',    'Asia/Colombo'),
    ('currency',    'LKR'),
    ('app_logo',    ''),
    ('app_email',   'info@aquacrm.test'),
    ('app_phone',   '+94 11 234 5678'),
    ('app_address', '123 Main Street, Colombo 03, Sri Lanka'),
    ('date_format', 'd/m/Y');

-- ------------------------------------------------------------
-- 16b. Service contracts – lifecycle state coverage
--      Adds expired, suspended, and cancelled contract examples
--      using existing customers so every status appears in the
--      Contracts list and contract-status report widget.
-- ------------------------------------------------------------
INSERT INTO `service_contracts`
    (`id`, `contract_code`, `customer_id`, `product_id`, `service_type_id`,
     `branch_id`, `installation_date`, `next_service_date`,
     `status`, `serial_number`, `assigned_technician_id`, `created_by`)
VALUES
    -- EXPIRED: D6M contract for Malini De Silva – ended naturally
    (13, 'CON-0013', 2, 1, 2, 1,
         '2023-01-10', '2023-07-10', 'expired', 'SN-DOM-20001', 3, 1),
    -- SUSPENDED: D9M contract for Saman Wickramasinghe – on hold
    (14, 'CON-0014', 5, 1, 3, 3,
         '2024-04-01', '2025-01-01', 'suspended', 'SN-DOM-20002', 7, 1),
    -- CANCELLED: D3M contract for Kumari Amarasinghe – cancelled by customer
    (15, 'CON-0015', 10, 1, 1, 5,
         '2024-07-15', '2024-10-15', 'cancelled', 'SN-DOM-20003', 3, 1);

-- ------------------------------------------------------------
-- 16c. Service jobs – status + job-type coverage
--      Adds: in_progress, cancelled statuses; repair, survey types
-- ------------------------------------------------------------
INSERT INTO `service_jobs`
    (`id`, `job_code`, `contract_id`, `customer_id`, `branch_id`, `job_type`,
     `status`, `priority`, `scheduled_date`, `scheduled_time`,
     `assigned_to`, `notes`, `cost`, `created_by`, `created_at`)
VALUES
    -- REPAIR + IN_PROGRESS: low pressure fault at Chaminda Perera
    (11, 'JOB-0011', 1, 1, 1, 'repair',
         'in_progress', 'high',
         CURDATE(), '10:00:00', 3,
         'Customer reports very low water pressure – pump and membrane inspection in progress.',
         1200.00, 1, NOW()),
    -- SURVEY + PENDING: pre-installation survey for a prospect in Galle
    (12, 'JOB-0012', NULL, 5, 3, 'survey',
         'pending', 'normal',
         DATE_ADD(CURDATE(), INTERVAL 3 DAY), '11:00:00', 7,
         'Pre-installation site survey for Saman Wickramasinghe – assess pipe work and filter location.',
         0.00, 1, NOW()),
    -- REPAIR + CANCELLED: repair booking cancelled by customer
    (13, 'JOB-0013', NULL, 3, 2, 'repair',
         'cancelled', 'normal',
         DATE_SUB(CURDATE(), INTERVAL 8 DAY), '14:00:00', 5,
         'Repair visit cancelled – customer requested reschedule. To be rebooked.',
         0.00, 1, DATE_SUB(NOW(), INTERVAL 9 DAY));

-- ------------------------------------------------------------
-- 16d. Update completed service jobs with completion_notes
--      and parts_used JSON so the job detail view is fully
--      populated for every completed job.
-- ------------------------------------------------------------
UPDATE `service_jobs` SET
    `completion_notes` = 'Domestic purifier (D6M) installed successfully. Full pressure test passed. TDS reading: 32 ppm. Customer briefed on filter schedule.',
    `parts_used`       = '[{"product_id":1,"name":"Domestic Water Purifier","quantity":1,"unit_price":15000.00}]'
WHERE `id` = 1;

UPDATE `service_jobs` SET
    `completion_notes` = 'D6M 6-month service complete. Pre-filter set replaced, membrane flushed, UV lamp checked. TDS: 28 ppm. Next service scheduled.',
    `parts_used`       = '[{"product_id":4,"name":"Pre-Filter Set (3-pack)","quantity":1,"unit_price":2200.00}]'
WHERE `id` = 2;

UPDATE `service_jobs` SET
    `completion_notes` = 'D6M domestic service completed for Saman Wickramasinghe. Filters and membrane replaced. Water quality confirmed within safe limits.',
    `parts_used`       = '[{"product_id":4,"name":"Pre-Filter Set (3-pack)","quantity":1,"unit_price":2200.00},{"product_id":3,"name":"Membrane Filter 50GPD","quantity":1,"unit_price":3500.00}]'
WHERE `id` = 3;

UPDATE `service_jobs` SET
    `completion_notes` = 'S1 commercial unit installed at Lanka Hotels Ltd. All six outlet points tested. TDS: 18 ppm. Anti-scale dosing system configured.',
    `parts_used`       = '[{"product_id":2,"name":"S1 Commercial Water Purifier","quantity":1,"unit_price":22000.00},{"product_id":5,"name":"Anti-Scale Chemical 5L","quantity":2,"unit_price":1800.00}]'
WHERE `id` = 4;

UPDATE `service_jobs` SET
    `completion_notes` = 'S1 commercial unit installed at Kandy Spice Garden. Kitchen and bar outlets tested. Customer satisfied – signed 12-month S112M service plan.',
    `parts_used`       = '[{"product_id":2,"name":"S1 Commercial Water Purifier","quantity":1,"unit_price":22000.00}]'
WHERE `id` = 5;

-- ------------------------------------------------------------
-- 16e. Leads – add a 'lost' lead to cover all pipeline stages
-- ------------------------------------------------------------
INSERT INTO `leads`
    (`id`, `lead_code`, `first_name`, `last_name`, `email`, `phone`,
     `address`, `city`, `branch_id`, `source`, `status`, `assigned_to`, `created_by`)
VALUES
    (7, 'LEAD-0007', 'Dharshan', 'Prasad', 'dharshan.prasad@example.com', '+94 71 301 0007',
        '6, Old Kandy Road, Kurunegala', 'Kurunegala', 2, 'online', 'lost', 4, 1);

-- ------------------------------------------------------------
-- 16f. Sales – add a cancelled order to cover all order statuses
-- ------------------------------------------------------------
INSERT INTO `sales`
    (`id`, `sale_code`, `customer_id`, `branch_id`, `sale_date`,
     `status`, `payment_status`, `subtotal`, `discount`, `tax`, `total`, `paid_amount`,
     `notes`, `created_by`, `created_at`)
VALUES
    (16, 'SALE-0016', 4, 2, DATE_SUB(CURDATE(), INTERVAL 5 MONTH),
         'cancelled', 'pending', 15000.00, 0.00, 0.00, 15000.00, 0.00,
         'Customer cancelled order – decided to defer purchase.',
         1, DATE_SUB(NOW(), INTERVAL 5 MONTH));

-- sale_item for the cancelled order (kept for referential integrity)
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `discount`, `total`)
VALUES (16, 1, 1, 15000.00, 0.00, 15000.00);

-- ------------------------------------------------------------
-- 16g. Attendance – add half_day and leave statuses
--      Uses dates 6–10 working days back (outside the existing
--      5-day window) so UNIQUE KEY (employee_id, date) is safe.
-- ------------------------------------------------------------
INSERT IGNORE INTO `attendance` (`employee_id`, `date`, `check_in`, `check_out`, `status`, `notes`) VALUES
-- Day -6: half_day examples
(1, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '09:00:00', '13:00:00', 'half_day', 'Doctor appointment afternoon'),
(3, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '09:00:00', '13:00:00', 'half_day', 'Personal errand'),
(5, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(7, DATE_SUB(CURDATE(), INTERVAL 6 DAY), '09:00:00', '17:00:00', 'present',  NULL),
-- Day -7: leave examples (approved annual / sick leave)
(2, DATE_SUB(CURDATE(), INTERVAL 7 DAY), NULL,        NULL,       'leave',    'Approved annual leave'),
(4, DATE_SUB(CURDATE(), INTERVAL 7 DAY), NULL,        NULL,       'leave',    'Approved sick leave'),
(6, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(8, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '09:00:00', '17:00:00', 'present',  NULL),
-- Day -8
(1, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '08:55:00', '17:00:00', 'present',  NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '08:45:00', '17:00:00', 'present',  NULL),
(4, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(5, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '08:30:00', '17:00:00', 'present',  NULL),
(6, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(7, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '09:10:00', '17:00:00', 'late',     NULL),
(8, DATE_SUB(CURDATE(), INTERVAL 8 DAY), '09:00:00', '17:00:00', 'present',  NULL),
-- Day -9
(1, DATE_SUB(CURDATE(), INTERVAL 9 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 9 DAY), NULL,        NULL,       'absent',   NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 9 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(4, DATE_SUB(CURDATE(), INTERVAL 9 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(5, DATE_SUB(CURDATE(), INTERVAL 9 DAY), '08:30:00', '17:00:00', 'present',  NULL),
(6, DATE_SUB(CURDATE(), INTERVAL 9 DAY), '08:45:00', '17:00:00', 'present',  NULL),
(7, DATE_SUB(CURDATE(), INTERVAL 9 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(8, DATE_SUB(CURDATE(), INTERVAL 9 DAY), '09:00:00', '17:00:00', 'present',  NULL),
-- Day -10
(1, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '08:50:00', '17:00:00', 'present',  NULL),
(2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(4, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:30:00', '17:00:00', 'late',     NULL),
(5, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '08:30:00', '13:00:00', 'half_day', 'Early leave approved'),
(6, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(7, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:00:00', '17:00:00', 'present',  NULL),
(8, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:00:00', '17:00:00', 'present',  NULL);

SET FOREIGN_KEY_CHECKS = 1;
