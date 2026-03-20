-- ============================================================
-- AquaCRM – Demo Seed Data
-- Run AFTER schema.sql has been executed.
-- Provides comprehensive demo data for every main process table.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- branches
-- ------------------------------------------------------------
INSERT IGNORE INTO `branches` (`id`, `name`, `address`, `city`, `state`, `phone`, `email`, `status`) VALUES
(1, 'Head Office',       '12 Water Lane, Downtown',   'Cityville',   'Central State', '555-0100', 'hq@aquacrm.test',     'active'),
(2, 'North Branch',      '45 River Road, Northside',  'Northtown',   'Central State', '555-0101', 'north@aquacrm.test',  'active'),
(3, 'South Branch',      '78 Spring St, Southpark',   'Southport',   'Central State', '555-0102', 'south@aquacrm.test',  'active'),
(4, 'East Branch',       '9 Lake Ave, Eastville',     'Eastwick',    'Eastern State', '555-0103', 'east@aquacrm.test',   'active'),
(5, 'West Service Hub',  '33 Oasis Blvd, Westend',    'Westburg',    'Western State', '555-0104', 'west@aquacrm.test',   'active');

-- ------------------------------------------------------------
-- users  (password hash = bcrypt of "Password1!")
-- ------------------------------------------------------------
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `role`, `branch_id`, `status`) VALUES
(1, 'Admin User',      'admin@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',       1, 'active'),
(2, 'Alice Manager',   'alice@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',     1, 'active'),
(3, 'Bob Technician',  'bob@aquacrm.test',      '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician',  2, 'active'),
(4, 'Carol Sales',     'carol@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff',       1, 'active'),
(5, 'David Staff',     'david@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff',       3, 'active'),
(6, 'Frank Costa',     'frank@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',     3, 'active'),
(7, 'Grace Lee',       'grace@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',     4, 'active'),
(8, 'Henry Patel',     'henry@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',     5, 'active');

-- ------------------------------------------------------------
-- customers
-- ------------------------------------------------------------
INSERT IGNORE INTO `customers` (`id`, `customer_code`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `state`, `postal_code`, `branch_id`, `source`, `status`, `created_by`) VALUES
(1, 'CUST-0001', 'James',   'Wilson',   'james.wilson@example.com',   '555-1001', '10 Maple St',      'Cityville',  'Central State', '10001', 1, 'referral',  'active',   1),
(2, 'CUST-0002', 'Sarah',   'Johnson',  'sarah.johnson@example.com',  '555-1002', '22 Oak Ave',       'Northtown',  'Central State', '10002', 2, 'online',    'active',   1),
(3, 'CUST-0003', 'Michael', 'Brown',    'michael.brown@example.com',  '555-1003', '7 Elm Drive',      'Southport',  'Central State', '10003', 3, 'walk_in',   'active',   1),
(4, 'CUST-0004', 'Emily',   'Davis',    'emily.davis@example.com',    '555-1004', '5 Pine Road',      'Eastwick',   'Eastern State', '10004', 4, 'lead_conversion', 'active', 1),
(5, 'CUST-0005', 'Robert',  'Martinez', 'robert.martinez@example.com','555-1005', '88 Cedar Lane',    'Westburg',   'Western State', '10005', 5, 'site_survey','active',  1);

-- ------------------------------------------------------------
-- leads
-- ------------------------------------------------------------
INSERT IGNORE INTO `leads` (`id`, `lead_code`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `branch_id`, `source`, `status`, `assigned_to`, `created_by`) VALUES
(1, 'LEAD-0001', 'Olivia',  'Taylor',  'olivia.taylor@example.com',  '555-2001', '14 Birch Ct',    'Cityville',  1, 'online',    'new',       2, 1),
(2, 'LEAD-0002', 'Liam',    'Anderson','liam.anderson@example.com',   '555-2002', '31 Spruce St',   'Northtown',  2, 'referral',  'contacted', 2, 1),
(3, 'LEAD-0003', 'Emma',    'Thomas',  'emma.thomas@example.com',    '555-2003', '6 Willow Way',   'Southport',  3, 'marketing', 'qualified', 4, 1),
(4, 'LEAD-0004', 'Noah',    'Jackson', 'noah.jackson@example.com',   '555-2004', '19 Ash Blvd',    'Eastwick',   4, 'walk_in',   'proposal',  4, 1),
(5, 'LEAD-0005', 'Ava',     'White',   'ava.white@example.com',      '555-2005', '55 Poplar Pl',   'Westburg',   5, 'other',     'won',       2, 1);

-- ------------------------------------------------------------
-- products
-- ------------------------------------------------------------
INSERT IGNORE INTO `products` (`id`, `product_code`, `name`, `description`, `category`, `brand`, `model_number`, `purchase_price`, `selling_price`, `service_interval_months`, `status`) VALUES
(1, 'PRD-001', 'AquaPure RO-5',          '5-stage reverse osmosis purifier for domestic use',   'purifier',    'AquaPure',   'RO-5-2024',  85.00,  150.00, 6,    'active'),
(2, 'PRD-002', 'AquaPure RO-7 Pro',      '7-stage RO purifier with UV sterilisation',           'purifier',    'AquaPure',   'RO-7-PRO',  120.00,  220.00, 6,    'active'),
(3, 'PRD-003', 'Membrane Filter 50GPD',  'Replacement RO membrane, 50 gallons per day',         'spare_part',  'FilterMax',  'MEM-50GPD',  18.00,   35.00, NULL, 'active'),
(4, 'PRD-004', 'Pre-Filter Set (3-pack)','Sediment + carbon block + carbon granule set',         'spare_part',  'FilterMax',  'PRE-3P',     12.00,   22.00, NULL, 'active'),
(5, 'PRD-005', 'Anti-Scale Chemical 5L', 'Liquid anti-scale treatment for water systems',        'chemical',    'ChemClear',  'AS-5L',       8.00,   18.00, NULL, 'active');

-- ------------------------------------------------------------
-- inventory_stock  (all 5 products × all 5 branches)
-- ------------------------------------------------------------
INSERT IGNORE INTO `inventory_stock` (`product_id`, `branch_id`, `quantity`, `min_quantity`) VALUES
(1, 1, 12, 3),
(2, 1,  8, 2),
(3, 1, 40, 10),
(4, 1, 25, 5),
(5, 1, 20, 5),
(1, 2,  8, 2),
(2, 2,  5, 2),
(3, 2, 30, 8),
(4, 2, 20, 5),
(5, 2, 15, 5),
(1, 3,  6, 2),
(2, 3,  4, 1),
(3, 3, 25, 6),
(4, 3, 18, 4),
(5, 3, 12, 3),
(1, 4, 10, 2),
(2, 4,  6, 1),
(3, 4, 35, 8),
(4, 4, 22, 5),
(5, 4, 18, 4),
(1, 5,  7, 2),
(2, 5,  3, 1),
(3, 5, 28, 7),
(4, 5, 15, 4),
(5, 5, 10, 3);

-- ------------------------------------------------------------
-- service_types
-- ------------------------------------------------------------
INSERT IGNORE INTO `service_types` (`id`, `code`, `name`, `description`, `interval_months`, `category`, `price`) VALUES
(1, 'DOM-Q', 'Domestic Quarterly Service', 'Full filter check and membrane flush every 3 months', 3, 'domestic',   450.00),
(2, 'DOM-H', 'Domestic Half-Yearly Service','Deep clean and filter replacement every 6 months',   6, 'domestic',   700.00),
(3, 'DOM-A', 'Domestic Annual Service',    'Annual overhaul with membrane and all filters',       12, 'domestic', 1200.00),
(4, 'COM-Q', 'Commercial Quarterly',       'Commercial-grade quarterly service',                   3, 'commercial',900.00),
(5, 'COM-A', 'Commercial Annual',          'Full commercial annual maintenance package',          12, 'commercial',2500.00);

-- ------------------------------------------------------------
-- employees
-- ------------------------------------------------------------
INSERT IGNORE INTO `employees` (`id`, `employee_code`, `first_name`, `last_name`, `email`, `phone`, `city`, `branch_id`, `department`, `position`, `date_joined`, `status`, `salary`, `user_id`) VALUES
(1, 'EMP-001', 'Alice',   'Manager',    'alice@aquacrm.test',   '555-3001', 'Cityville',  1, 'management', 'Branch Manager',    '2020-01-15', 'active', 3500.00, 2),
(2, 'EMP-002', 'Bob',     'Technician', 'bob@aquacrm.test',     '555-3002', 'Northtown',  2, 'technical',  'Senior Technician', '2020-06-01', 'active', 2200.00, 3),
(3, 'EMP-003', 'Carol',   'Sales',      'carol@aquacrm.test',   '555-3003', 'Cityville',  1, 'sales',      'Sales Executive',   '2021-03-10', 'active', 2000.00, 4),
(4, 'EMP-004', 'David',   'Staff',      'david@aquacrm.test',   '555-3004', 'Southport',  3, 'technical',  'Technician',        '2021-09-20', 'active', 1900.00, 5),
(5, 'EMP-005', 'Eve',     'Admin',      'eve@aquacrm.test',     '555-3005', 'Cityville',  1, 'admin',      'Admin Officer',     '2022-02-01', 'active', 1800.00, NULL),
(6, 'EMP-006', 'Frank',   'Costa',      'frank@aquacrm.test',   '555-3006', 'Southport',  3, 'management', 'Branch Manager',    '2021-01-10', 'active', 3200.00, 6),
(7, 'EMP-007', 'Grace',   'Lee',        'grace@aquacrm.test',   '555-3007', 'Eastwick',   4, 'management', 'Branch Manager',    '2021-06-15', 'active', 3200.00, 7),
(8, 'EMP-008', 'Henry',   'Patel',      'henry@aquacrm.test',   '555-3008', 'Westburg',   5, 'management', 'Branch Manager',    '2022-03-01', 'active', 3200.00, 8);

-- set branch managers (JOIN ensures the UPDATE is silently skipped if the employee does not exist yet)
UPDATE `branches` b JOIN `employees` e ON e.`id` = 1 SET b.`manager_id` = 1 WHERE b.`id` = 1;
UPDATE `branches` b JOIN `employees` e ON e.`id` = 2 SET b.`manager_id` = 2 WHERE b.`id` = 2;
UPDATE `branches` b JOIN `employees` e ON e.`id` = 6 SET b.`manager_id` = 6 WHERE b.`id` = 3;
UPDATE `branches` b JOIN `employees` e ON e.`id` = 7 SET b.`manager_id` = 7 WHERE b.`id` = 4;
UPDATE `branches` b JOIN `employees` e ON e.`id` = 8 SET b.`manager_id` = 8 WHERE b.`id` = 5;

-- ------------------------------------------------------------
-- service_contracts
-- ------------------------------------------------------------
INSERT IGNORE INTO `service_contracts` (`id`, `contract_code`, `customer_id`, `product_id`, `service_type_id`, `branch_id`, `installation_date`, `next_service_date`, `status`, `serial_number`, `assigned_technician_id`, `created_by`) VALUES
(1, 'CON-0001', 1, 1, 1, 1, '2025-01-10', '2025-10-10', 'active',  'SN-RO5-10001', 2, 1),
(2, 'CON-0002', 2, 2, 2, 2, '2025-02-15', '2025-08-15', 'active',  'SN-RO7-10002', 2, 1),
(3, 'CON-0003', 3, 1, 3, 3, '2024-11-20', '2025-11-20', 'active',  'SN-RO5-10003', 4, 1),
(4, 'CON-0004', 4, 2, 1, 4, '2025-03-01', '2025-12-01', 'active',  'SN-RO7-10004', 4, 1),
(5, 'CON-0005', 5, 1, 2, 5, '2025-04-05', '2025-10-05', 'active',  'SN-RO5-10005', 2, 1);

-- ------------------------------------------------------------
-- service_jobs
-- ------------------------------------------------------------
INSERT IGNORE INTO `service_jobs` (`id`, `job_code`, `contract_id`, `customer_id`, `branch_id`, `job_type`, `status`, `priority`, `scheduled_date`, `scheduled_time`, `assigned_to`, `notes`, `cost`, `created_by`) VALUES
(1, 'JOB-0001', 1, 1, 1, 'maintenance',   'completed',   'normal', '2025-07-10', '09:00:00', 2, 'Quarterly filter flush completed',        450.00, 1),
(2, 'JOB-0002', 2, 2, 2, 'maintenance',   'in_progress', 'normal', '2025-08-15', '10:00:00', 2, 'Half-yearly deep clean in progress',      700.00, 1),
(3, 'JOB-0003', 3, 3, 3, 'installation',  'completed',   'high',   '2024-11-20', '08:30:00', 4, 'New unit installed successfully',         150.00, 1),
(4, 'JOB-0004', 4, 4, 4, 'repair',        'pending',     'urgent', '2025-09-01', '11:00:00', 4, 'Customer reports low pressure issue',       80.00, 1),
(5, 'JOB-0005', 5, 5, 5, 'survey',        'assigned',    'low',    '2025-09-05', '14:00:00', 2, 'Pre-service site survey scheduled',         50.00, 1);

-- ------------------------------------------------------------
-- sales
-- ------------------------------------------------------------
INSERT IGNORE INTO `sales` (`id`, `sale_code`, `customer_id`, `branch_id`, `sale_date`, `status`, `payment_status`, `subtotal`, `discount`, `tax`, `total`, `paid_amount`, `notes`, `created_by`) VALUES
(1, 'SALE-0001', 1, 1, '2025-01-10', 'delivered',  'paid',     150.00,  0.00, 0.00, 150.00, 150.00, 'RO-5 unit purchase on installation day', 1),
(2, 'SALE-0002', 2, 2, '2025-02-15', 'delivered',  'paid',     220.00,  0.00, 0.00, 220.00, 220.00, 'RO-7 Pro sale',                          1),
(3, 'SALE-0003', 3, 3, '2025-04-20', 'confirmed',  'partial',   57.00,  5.00, 0.00,  52.00,  30.00, 'Spare parts order',                      1),
(4, 'SALE-0004', 4, 4, '2025-06-01', 'quotation',  'pending',  220.00, 10.00, 0.00, 210.00,   0.00, 'Quotation pending approval',             1),
(5, 'SALE-0005', 5, 5, '2025-07-15', 'confirmed',  'paid',      36.00,  0.00, 0.00,  36.00,  36.00, 'Chemical and filter set sale',           1);

-- ------------------------------------------------------------
-- sale_items
-- ------------------------------------------------------------
INSERT IGNORE INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `discount`, `total`) VALUES
(1, 1, 1, 150.00, 0.00, 150.00),
(2, 2, 1, 220.00, 0.00, 220.00),
(3, 3, 1,  35.00, 5.00,  30.00),
(3, 4, 1,  22.00, 0.00,  22.00),
(4, 2, 1, 220.00,10.00, 210.00),
(5, 5, 1,  18.00, 0.00,  18.00),
(5, 4, 1,  22.00, 4.00,  18.00);

-- ------------------------------------------------------------
-- attendance  (5 working days × 8 employees)
-- ------------------------------------------------------------
INSERT IGNORE INTO `attendance` (`employee_id`, `date`, `check_in`, `check_out`, `status`) VALUES
(1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:55:00', '17:05:00', 'present'),
(1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:10:00', '17:00:00', 'late'),
(1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '08:50:00', '17:00:00', 'present'),
(1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:00:00', '17:00:00', 'present'),
(1, CURDATE(),                           '09:05:00', '17:00:00', 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00', 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:15:00', '17:00:00', 'late'),
(2, CURDATE(),                           '09:00:00', '17:00:00', 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:45:00', '17:15:00', 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00', 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 1 DAY), NULL,        NULL,       'absent'),
(3, CURDATE(),                           '08:30:00', '17:00:00', 'present'),
(4, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(4, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(4, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:30:00', '17:00:00', 'late'),
(4, DATE_SUB(CURDATE(), INTERVAL 1 DAY), NULL,        NULL,       'absent'),
(4, CURDATE(),                           '09:00:00', '17:00:00', 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:30:00', '17:00:00', 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '08:30:00', '17:00:00', 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '08:45:00', '17:00:00', 'present'),
(5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:00:00', '17:00:00', 'present'),
(5, CURDATE(),                           '08:30:00', '17:00:00', 'present'),
(6, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(6, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(6, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00', 'present'),
(6, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:10:00', '17:00:00', 'late'),
(6, CURDATE(),                           '09:00:00', '17:00:00', 'present'),
(7, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:45:00', '17:00:00', 'present'),
(7, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '08:45:00', '17:00:00', 'present'),
(7, DATE_SUB(CURDATE(), INTERVAL 2 DAY), NULL,        NULL,       'absent'),
(7, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '08:45:00', '17:00:00', 'present'),
(7, CURDATE(),                           '08:45:00', '17:00:00', 'present'),
(8, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(8, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(8, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '09:00:00', '17:00:00', 'present'),
(8, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '09:00:00', '17:00:00', 'present'),
(8, CURDATE(),                           '09:00:00', '17:00:00', 'present');

-- ------------------------------------------------------------
-- leave_requests
-- ------------------------------------------------------------
INSERT IGNORE INTO `leave_requests` (`employee_id`, `leave_type`, `start_date`, `end_date`, `days`, `reason`, `status`, `approved_by`) VALUES
(2, 'annual',    '2025-08-04', '2025-08-06', 3, 'Family vacation',         'approved',  1),
(3, 'sick',      '2025-07-21', '2025-07-21', 1, 'Fever',                   'approved',  1),
(4, 'casual',    '2025-09-10', '2025-09-10', 1, 'Personal errand',         'pending',  NULL),
(5, 'annual',    '2025-10-01', '2025-10-03', 3, 'Annual leave',            'pending',  NULL),
(1, 'maternity', '2026-01-15', '2026-04-15',60, 'Maternity leave',         'approved',  1),
(6, 'annual',    '2025-12-20', '2025-12-24', 5, 'Year-end holiday',        'approved',  1),
(7, 'sick',      '2025-11-03', '2025-11-03', 1, 'Not feeling well',        'approved',  1),
(8, 'casual',    '2026-02-14', '2026-02-14', 1, 'Personal errand',         'pending',  NULL);

-- ============================================================
-- Dashboard demo data
-- These records use relative dates (CURDATE() / DATE_ADD / DATE_SUB)
-- so they will always appear in the correct dashboard sections
-- no matter when the seed is run.
-- ============================================================

-- ------------------------------------------------------------
-- Additional customers (used by the new jobs and sales below)
-- ------------------------------------------------------------
INSERT IGNORE INTO `customers` (`id`, `customer_code`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `state`, `postal_code`, `branch_id`, `source`, `status`, `created_by`) VALUES
(6,  'CUST-0006', 'Linda',   'Chen',    'linda.chen@example.com',    '555-1006', '3 Jasmine Close',  'Cityville',  'Central State', '10006', 1, 'referral',        'active', 1),
(7,  'CUST-0007', 'Omar',    'Hassan',  'omar.hassan@example.com',   '555-1007', '20 Mango Way',     'Northtown',  'Central State', '10007', 2, 'online',          'active', 1),
(8,  'CUST-0008', 'Priya',   'Sharma',  'priya.sharma@example.com',  '555-1008', '55 Rose Garden',   'Southport',  'Central State', '10008', 3, 'walk_in',         'active', 1),
(9,  'CUST-0009', 'Jack',    'Robinson','jack.robinson@example.com', '555-1009', '8 Timber Lane',    'Eastwick',   'Eastern State', '10009', 4, 'lead_conversion', 'active', 1),
(10, 'CUST-0010', 'Fiona',   'Ng',      'fiona.ng@example.com',      '555-1010', '12 Orchid Court',  'Westburg',   'Western State', '10010', 5, 'site_survey',     'active', 1);

-- ------------------------------------------------------------
-- Additional service_contracts for the new customers
-- next_service_date is set relative to today so the contracts
-- line up naturally with the jobs below.
-- ------------------------------------------------------------
INSERT IGNORE INTO `service_contracts` (`id`, `contract_code`, `customer_id`, `product_id`, `service_type_id`, `branch_id`, `installation_date`, `next_service_date`, `status`, `serial_number`, `assigned_technician_id`, `created_by`) VALUES
(6,  'CON-0006', 6,  1, 1, 1, DATE_SUB(CURDATE(), INTERVAL 90  DAY), DATE_ADD(CURDATE(), INTERVAL 2  DAY), 'active', 'SN-RO5-10006', 2, 1),
(7,  'CON-0007', 7,  2, 2, 2, DATE_SUB(CURDATE(), INTERVAL 180 DAY), DATE_ADD(CURDATE(), INTERVAL 1  DAY), 'active', 'SN-RO7-10007', 2, 1),
(8,  'CON-0008', 8,  1, 3, 3, DATE_SUB(CURDATE(), INTERVAL 365 DAY), DATE_ADD(CURDATE(), INTERVAL 4  DAY), 'active', 'SN-RO5-10008', 4, 1),
(9,  'CON-0009', 9,  2, 1, 4, DATE_SUB(CURDATE(), INTERVAL 60  DAY), DATE_ADD(CURDATE(), INTERVAL 5  DAY), 'active', 'SN-RO7-10009', 4, 1),
(10, 'CON-0010', 10, 1, 2, 5, DATE_SUB(CURDATE(), INTERVAL 150 DAY), DATE_ADD(CURDATE(), INTERVAL 6  DAY), 'active', 'SN-RO5-10010', 2, 1);

-- ------------------------------------------------------------
-- service_jobs – scheduled THIS WEEK (relative to CURDATE())
-- Populates:
--   "Services Due This Week"  – scheduled_date within next 7 days,
--                               status not completed/cancelled
--   "Recent Jobs"             – created_at = NOW() so these sort
--                               to the top of the last-5 list
-- ------------------------------------------------------------
INSERT IGNORE INTO `service_jobs` (`id`, `job_code`, `contract_id`, `customer_id`, `branch_id`, `job_type`, `status`, `priority`, `scheduled_date`, `scheduled_time`, `assigned_to`, `notes`, `cost`, `created_by`) VALUES
(6,  'JOB-0006', 6,  6,  1, 'maintenance',  'pending',     'normal', CURDATE(),                           '09:00:00', 2, 'Quarterly filter flush – due today',              450.00, 1),
(7,  'JOB-0007', 7,  7,  2, 'maintenance',  'assigned',    'high',   DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', 2, 'Half-yearly deep clean – due tomorrow',           700.00, 1),
(8,  'JOB-0008', 8,  8,  3, 'repair',       'pending',     'urgent', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '08:00:00', 4, 'Customer reports low flow rate',                   80.00, 1),
(9,  'JOB-0009', 9,  9,  4, 'installation', 'assigned',    'normal', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '11:00:00', 4, 'New RO-7 Pro installation at customer premises',  150.00, 1),
(10, 'JOB-0010', 10, 10, 5, 'survey',       'in_progress', 'low',    DATE_ADD(CURDATE(), INTERVAL 6 DAY), '14:00:00', 2, 'Pre-installation site survey',                     50.00, 1);

-- ------------------------------------------------------------
-- sales – current-week dates (relative)
-- Populates:
--   "Recent Sales"  – created_at = NOW() so these sort to the
--                     top of the last-5 list; status != cancelled
--   Revenue chart   – sale_date within last 12 months
-- ------------------------------------------------------------
INSERT IGNORE INTO `sales` (`id`, `sale_code`, `customer_id`, `branch_id`, `sale_date`, `status`, `payment_status`, `subtotal`, `discount`, `tax`, `total`, `paid_amount`, `notes`, `created_by`) VALUES
(6,  'SALE-0006', 6,  1, CURDATE(),                           'delivered', 'paid',    150.00,  0.00, 0.00, 150.00, 150.00, 'RO-5 replacement unit',                    1),
(7,  'SALE-0007', 7,  2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'confirmed', 'partial', 220.00,  0.00, 0.00, 220.00, 110.00, 'RO-7 Pro upgrade – first instalment paid', 1),
(8,  'SALE-0008', 8,  3, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'delivered', 'paid',     57.00,  0.00, 0.00,  57.00,  57.00, 'Membrane + pre-filter set replacement',    1),
(9,  'SALE-0009', 9,  4, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'confirmed', 'pending', 220.00, 10.00, 0.00, 210.00,   0.00, 'Commercial unit – pending payment',        1),
(10, 'SALE-0010', 10, 5, DATE_SUB(CURDATE(), INTERVAL 4 DAY), 'delivered', 'paid',     36.00,  0.00, 0.00,  36.00,  36.00, 'Anti-scale chemical treatment pack',       1);

-- sale_items for the new sales
INSERT IGNORE INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `discount`, `total`) VALUES
(6,  1, 1, 150.00,  0.00, 150.00),
(7,  2, 1, 220.00,  0.00, 220.00),
(8,  3, 1,  35.00,  0.00,  35.00),
(8,  4, 1,  22.00,  0.00,  22.00),
(9,  2, 1, 220.00, 10.00, 210.00),
(10, 5, 1,  18.00,  0.00,  18.00),
(10, 4, 1,  22.00,  4.00,  18.00);

SET FOREIGN_KEY_CHECKS = 1;
