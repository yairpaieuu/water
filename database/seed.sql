-- ============================================================
-- AquaCRM – Demo Seed Data
-- Run AFTER schema.sql has been executed.
-- Provides 5 demo rows for every main process table.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- branches
-- ------------------------------------------------------------
INSERT INTO `branches` (`id`, `name`, `address`, `city`, `state`, `phone`, `email`, `status`) VALUES
(1, 'Head Office',       '12 Water Lane, Downtown',   'Cityville',   'Central State', '555-0100', 'hq@aquacrm.test',     'active'),
(2, 'North Branch',      '45 River Road, Northside',  'Northtown',   'Central State', '555-0101', 'north@aquacrm.test',  'active'),
(3, 'South Branch',      '78 Spring St, Southpark',   'Southport',   'Central State', '555-0102', 'south@aquacrm.test',  'active'),
(4, 'East Branch',       '9 Lake Ave, Eastville',     'Eastwick',    'Eastern State', '555-0103', 'east@aquacrm.test',   'active'),
(5, 'West Service Hub',  '33 Oasis Blvd, Westend',    'Westburg',    'Western State', '555-0104', 'west@aquacrm.test',   'active');

-- ------------------------------------------------------------
-- users  (password hash = bcrypt of "Password1!")
-- ------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `branch_id`, `status`) VALUES
(1, 'Admin User',      'admin@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',       1, 'active'),
(2, 'Alice Manager',   'alice@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager',     1, 'active'),
(3, 'Bob Technician',  'bob@aquacrm.test',      '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician',  2, 'active'),
(4, 'Carol Sales',     'carol@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff',       1, 'active'),
(5, 'David Staff',     'david@aquacrm.test',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff',       3, 'active');

-- ------------------------------------------------------------
-- customers
-- ------------------------------------------------------------
INSERT INTO `customers` (`id`, `customer_code`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `state`, `postal_code`, `branch_id`, `source`, `status`, `created_by`) VALUES
(1, 'CUST-0001', 'James',   'Wilson',   'james.wilson@example.com',   '555-1001', '10 Maple St',      'Cityville',  'Central State', '10001', 1, 'referral',  'active',   1),
(2, 'CUST-0002', 'Sarah',   'Johnson',  'sarah.johnson@example.com',  '555-1002', '22 Oak Ave',       'Northtown',  'Central State', '10002', 2, 'online',    'active',   1),
(3, 'CUST-0003', 'Michael', 'Brown',    'michael.brown@example.com',  '555-1003', '7 Elm Drive',      'Southport',  'Central State', '10003', 3, 'walk_in',   'active',   1),
(4, 'CUST-0004', 'Emily',   'Davis',    'emily.davis@example.com',    '555-1004', '5 Pine Road',      'Eastwick',   'Eastern State', '10004', 4, 'lead_conversion', 'active', 1),
(5, 'CUST-0005', 'Robert',  'Martinez', 'robert.martinez@example.com','555-1005', '88 Cedar Lane',    'Westburg',   'Western State', '10005', 5, 'site_survey','active',  1);

-- ------------------------------------------------------------
-- leads
-- ------------------------------------------------------------
INSERT INTO `leads` (`id`, `lead_code`, `first_name`, `last_name`, `email`, `phone`, `address`, `city`, `branch_id`, `source`, `status`, `assigned_to`, `created_by`) VALUES
(1, 'LEAD-0001', 'Olivia',  'Taylor',  'olivia.taylor@example.com',  '555-2001', '14 Birch Ct',    'Cityville',  1, 'online',    'new',       2, 1),
(2, 'LEAD-0002', 'Liam',    'Anderson','liam.anderson@example.com',   '555-2002', '31 Spruce St',   'Northtown',  2, 'referral',  'contacted', 2, 1),
(3, 'LEAD-0003', 'Emma',    'Thomas',  'emma.thomas@example.com',    '555-2003', '6 Willow Way',   'Southport',  3, 'marketing', 'qualified', 4, 1),
(4, 'LEAD-0004', 'Noah',    'Jackson', 'noah.jackson@example.com',   '555-2004', '19 Ash Blvd',    'Eastwick',   4, 'walk_in',   'proposal',  4, 1),
(5, 'LEAD-0005', 'Ava',     'White',   'ava.white@example.com',      '555-2005', '55 Poplar Pl',   'Westburg',   5, 'other',     'won',       2, 1);

-- ------------------------------------------------------------
-- products
-- ------------------------------------------------------------
INSERT INTO `products` (`id`, `product_code`, `name`, `description`, `category`, `brand`, `model_number`, `purchase_price`, `selling_price`, `service_interval_months`, `status`) VALUES
(1, 'PRD-001', 'AquaPure RO-5',          '5-stage reverse osmosis purifier for domestic use',   'purifier',    'AquaPure',   'RO-5-2024',  85.00,  150.00, 6,    'active'),
(2, 'PRD-002', 'AquaPure RO-7 Pro',      '7-stage RO purifier with UV sterilisation',           'purifier',    'AquaPure',   'RO-7-PRO',  120.00,  220.00, 6,    'active'),
(3, 'PRD-003', 'Membrane Filter 50GPD',  'Replacement RO membrane, 50 gallons per day',         'spare_part',  'FilterMax',  'MEM-50GPD',  18.00,   35.00, NULL, 'active'),
(4, 'PRD-004', 'Pre-Filter Set (3-pack)','Sediment + carbon block + carbon granule set',         'spare_part',  'FilterMax',  'PRE-3P',     12.00,   22.00, NULL, 'active'),
(5, 'PRD-005', 'Anti-Scale Chemical 5L', 'Liquid anti-scale treatment for water systems',        'chemical',    'ChemClear',  'AS-5L',       8.00,   18.00, NULL, 'active');

-- ------------------------------------------------------------
-- inventory_stock  (branch 1 & 2)
-- ------------------------------------------------------------
INSERT INTO `inventory_stock` (`product_id`, `branch_id`, `quantity`, `min_quantity`) VALUES
(1, 1, 12, 3),
(2, 1,  8, 2),
(3, 1, 40, 10),
(4, 1, 25, 5),
(5, 2, 15, 5);

-- ------------------------------------------------------------
-- service_types
-- ------------------------------------------------------------
INSERT INTO `service_types` (`id`, `code`, `name`, `description`, `interval_months`, `category`, `price`) VALUES
(1, 'DOM-Q', 'Domestic Quarterly Service', 'Full filter check and membrane flush every 3 months', 3, 'domestic',   450.00),
(2, 'DOM-H', 'Domestic Half-Yearly Service','Deep clean and filter replacement every 6 months',   6, 'domestic',   700.00),
(3, 'DOM-A', 'Domestic Annual Service',    'Annual overhaul with membrane and all filters',       12, 'domestic', 1200.00),
(4, 'COM-Q', 'Commercial Quarterly',       'Commercial-grade quarterly service',                   3, 'commercial',900.00),
(5, 'COM-A', 'Commercial Annual',          'Full commercial annual maintenance package',          12, 'commercial',2500.00);

-- ------------------------------------------------------------
-- employees
-- ------------------------------------------------------------
INSERT INTO `employees` (`id`, `employee_code`, `first_name`, `last_name`, `email`, `phone`, `city`, `branch_id`, `department`, `position`, `date_joined`, `status`, `salary`, `user_id`) VALUES
(1, 'EMP-001', 'Alice',   'Manager',    'alice@aquacrm.test',   '555-3001', 'Cityville',  1, 'management', 'Branch Manager',    '2020-01-15', 'active', 3500.00, 2),
(2, 'EMP-002', 'Bob',     'Technician', 'bob@aquacrm.test',     '555-3002', 'Northtown',  2, 'technical',  'Senior Technician', '2020-06-01', 'active', 2200.00, 3),
(3, 'EMP-003', 'Carol',   'Sales',      'carol@aquacrm.test',   '555-3003', 'Cityville',  1, 'sales',      'Sales Executive',   '2021-03-10', 'active', 2000.00, 4),
(4, 'EMP-004', 'David',   'Staff',      'david@aquacrm.test',   '555-3004', 'Southport',  3, 'technical',  'Technician',        '2021-09-20', 'active', 1900.00, 5),
(5, 'EMP-005', 'Eve',     'Admin',      'eve@aquacrm.test',     '555-3005', 'Cityville',  1, 'admin',      'Admin Officer',     '2022-02-01', 'active', 1800.00, NULL);

-- set branch managers
UPDATE `branches` SET `manager_id` = 1 WHERE `id` = 1;
UPDATE `branches` SET `manager_id` = 2 WHERE `id` = 2;

-- ------------------------------------------------------------
-- service_contracts
-- ------------------------------------------------------------
INSERT INTO `service_contracts` (`id`, `contract_code`, `customer_id`, `product_id`, `service_type_id`, `branch_id`, `installation_date`, `next_service_date`, `status`, `serial_number`, `assigned_technician_id`, `created_by`) VALUES
(1, 'CON-0001', 1, 1, 1, 1, '2025-01-10', '2025-10-10', 'active',  'SN-RO5-10001', 2, 1),
(2, 'CON-0002', 2, 2, 2, 2, '2025-02-15', '2025-08-15', 'active',  'SN-RO7-10002', 2, 1),
(3, 'CON-0003', 3, 1, 3, 3, '2024-11-20', '2025-11-20', 'active',  'SN-RO5-10003', 4, 1),
(4, 'CON-0004', 4, 2, 1, 4, '2025-03-01', '2025-12-01', 'active',  'SN-RO7-10004', 4, 1),
(5, 'CON-0005', 5, 1, 2, 5, '2025-04-05', '2025-10-05', 'active',  'SN-RO5-10005', 2, 1);

-- ------------------------------------------------------------
-- service_jobs
-- ------------------------------------------------------------
INSERT INTO `service_jobs` (`id`, `job_code`, `contract_id`, `customer_id`, `branch_id`, `job_type`, `status`, `priority`, `scheduled_date`, `scheduled_time`, `assigned_to`, `notes`, `cost`, `created_by`) VALUES
(1, 'JOB-0001', 1, 1, 1, 'maintenance',   'completed',   'normal', '2025-07-10', '09:00:00', 2, 'Quarterly filter flush completed',        450.00, 1),
(2, 'JOB-0002', 2, 2, 2, 'maintenance',   'in_progress', 'normal', '2025-08-15', '10:00:00', 2, 'Half-yearly deep clean in progress',      700.00, 1),
(3, 'JOB-0003', 3, 3, 3, 'installation',  'completed',   'high',   '2024-11-20', '08:30:00', 4, 'New unit installed successfully',         150.00, 1),
(4, 'JOB-0004', 4, 4, 4, 'repair',        'pending',     'urgent', '2025-09-01', '11:00:00', 4, 'Customer reports low pressure issue',       80.00, 1),
(5, 'JOB-0005', 5, 5, 5, 'survey',        'assigned',    'low',    '2025-09-05', '14:00:00', 2, 'Pre-service site survey scheduled',         50.00, 1);

-- ------------------------------------------------------------
-- sales
-- ------------------------------------------------------------
INSERT INTO `sales` (`id`, `sale_code`, `customer_id`, `branch_id`, `sale_date`, `status`, `payment_status`, `subtotal`, `discount`, `tax`, `total`, `paid_amount`, `notes`, `created_by`) VALUES
(1, 'SALE-0001', 1, 1, '2025-01-10', 'delivered',  'paid',     150.00,  0.00, 0.00, 150.00, 150.00, 'RO-5 unit purchase on installation day', 1),
(2, 'SALE-0002', 2, 2, '2025-02-15', 'delivered',  'paid',     220.00,  0.00, 0.00, 220.00, 220.00, 'RO-7 Pro sale',                          1),
(3, 'SALE-0003', 3, 3, '2025-04-20', 'confirmed',  'partial',   57.00,  5.00, 0.00,  52.00,  30.00, 'Spare parts order',                      1),
(4, 'SALE-0004', 4, 4, '2025-06-01', 'quotation',  'pending',  220.00, 10.00, 0.00, 210.00,   0.00, 'Quotation pending approval',             1),
(5, 'SALE-0005', 5, 5, '2025-07-15', 'confirmed',  'paid',      36.00,  0.00, 0.00,  36.00,  36.00, 'Chemical and filter set sale',           1);

-- ------------------------------------------------------------
-- sale_items
-- ------------------------------------------------------------
INSERT INTO `sale_items` (`sale_id`, `product_id`, `quantity`, `unit_price`, `discount`, `total`) VALUES
(1, 1, 1, 150.00, 0.00, 150.00),
(2, 2, 1, 220.00, 0.00, 220.00),
(3, 3, 1,  35.00, 5.00,  30.00),
(3, 4, 1,  22.00, 0.00,  22.00),
(4, 2, 1, 220.00,10.00, 210.00),
(5, 5, 1,  18.00, 0.00,  18.00),
(5, 4, 1,  22.00, 4.00,  18.00);

-- ------------------------------------------------------------
-- attendance  (5 records for EMP-001 over last 5 working days)
-- ------------------------------------------------------------
INSERT INTO `attendance` (`employee_id`, `date`, `check_in`, `check_out`, `status`) VALUES
(1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:55:00', '17:05:00', 'present'),
(1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:10:00', '17:00:00', 'late'),
(2, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '09:00:00', '17:00:00', 'present'),
(2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', '17:00:00', 'present'),
(3, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '08:45:00', '17:15:00', 'present');

-- ------------------------------------------------------------
-- leave_requests
-- ------------------------------------------------------------
INSERT INTO `leave_requests` (`employee_id`, `leave_type`, `start_date`, `end_date`, `days`, `reason`, `status`, `approved_by`) VALUES
(2, 'annual',    '2025-08-04', '2025-08-06', 3, 'Family vacation',         'approved',  1),
(3, 'sick',      '2025-07-21', '2025-07-21', 1, 'Fever',                   'approved',  1),
(4, 'casual',    '2025-09-10', '2025-09-10', 1, 'Personal errand',         'pending',  NULL),
(5, 'annual',    '2025-10-01', '2025-10-03', 3, 'Annual leave',            'pending',  NULL),
(1, 'maternity', '2026-01-15', '2026-04-15',60, 'Maternity leave',         'approved',  1);

SET FOREIGN_KEY_CHECKS = 1;
