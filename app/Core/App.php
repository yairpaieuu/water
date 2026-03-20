<?php
declare(strict_types=1);

namespace App\Core;

class App
{
    private static array $config = [];

    public static function run(): void
    {
        self::loadConfig();
        self::configureEnvironment();
        Session::start();
        $router = self::buildRouter();
        $router->dispatch();
    }

    public static function config(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    private static function loadConfig(): void
    {
        self::$config = require CONFIG_PATH . '/app.php';
    }

    private static function configureEnvironment(): void
    {
        $timezone = self::config('app.timezone', 'UTC');
        date_default_timezone_set($timezone);

        if (self::config('app.env') === 'production') {
            error_reporting(0);
            ini_set('display_errors', '0');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }
    }

    private static function buildRouter(): Router
    {
        $router = new Router();

        // Auth
        $router->get('/login',  'AuthController@showLogin');
        $router->post('/login', 'AuthController@login');
        $router->get('/logout', 'AuthController@logout');

        // Dashboard
        $router->get('/',          'DashboardController@index', ['auth']);
        $router->get('/dashboard', 'DashboardController@index', ['auth']);

        // Customers
        $router->get('/customers',                'CustomerController@index',  ['auth']);
        $router->get('/customers/create',         'CustomerController@create', ['auth']);
        $router->post('/customers/store',         'CustomerController@store',  ['auth']);
        $router->get('/customers/{id}',           'CustomerController@show',   ['auth']);
        $router->get('/customers/{id}/edit',      'CustomerController@edit',   ['auth']);
        $router->post('/customers/{id}/update',   'CustomerController@update', ['auth']);
        $router->post('/customers/{id}/delete',   'CustomerController@delete', ['auth']);

        // Leads
        $router->get('/leads',                'LeadController@index',   ['auth']);
        $router->get('/leads/create',         'LeadController@create',  ['auth']);
        $router->post('/leads/store',         'LeadController@store',   ['auth']);
        $router->get('/leads/{id}',           'LeadController@show',    ['auth']);
        $router->post('/leads/{id}/update',   'LeadController@update',  ['auth']);
        $router->post('/leads/{id}/delete',   'LeadController@delete',  ['auth']);
        $router->post('/leads/{id}/convert',  'LeadController@convert', ['auth']);

        // Sales / Orders
        $router->get('/orders',               'SalesController@index',  ['auth']);
        $router->get('/orders/create',        'SalesController@create', ['auth']);
        $router->post('/orders/store',        'SalesController@store',  ['auth']);
        $router->get('/orders/{id}',          'SalesController@show',   ['auth']);
        $router->post('/orders/{id}/update',  'SalesController@update', ['auth']);
        $router->post('/orders/{id}/delete',  'SalesController@delete', ['auth']);

        // Inventory / Products
        $router->get('/inventory',              'InventoryController@index',       ['auth']);
        $router->get('/inventory/create',       'InventoryController@create',      ['auth']);
        $router->post('/inventory/store',       'InventoryController@store',       ['auth']);
        $router->post('/inventory/{id}/update', 'InventoryController@update',      ['auth']);
        $router->post('/stock/adjust',          'InventoryController@adjustStock', ['auth']);

        // Service Jobs
        $router->get('/jobs',               'ServiceController@indexJobs',  ['auth']);
        $router->get('/jobs/create',        'ServiceController@createJob',  ['auth']);
        $router->post('/jobs/store',        'ServiceController@storeJob',   ['auth']);
        $router->get('/jobs/{id}',          'ServiceController@showJob',    ['auth']);
        $router->post('/jobs/{id}/update',  'ServiceController@updateJob',  ['auth']);

        // Service Contracts
        $router->get('/contracts',            'ServiceController@indexContracts',  ['auth']);
        $router->get('/contracts/create',     'ServiceController@createContract',  ['auth']);
        $router->post('/contracts/store',     'ServiceController@storeContract',   ['auth']);
        $router->get('/contracts/{id}',       'ServiceController@showContract',    ['auth']);

        // Employees
        $router->get('/employees',              'EmployeeController@index',  ['auth']);
        $router->get('/employees/create',       'EmployeeController@create', ['auth']);
        $router->post('/employees/store',       'EmployeeController@store',  ['auth']);
        $router->get('/employees/{id}',         'EmployeeController@show',   ['auth']);
        $router->post('/employees/{id}/update', 'EmployeeController@update', ['auth']);

        // Attendance
        $router->get('/attendance',        'EmployeeController@indexAttendance',  ['auth']);
        $router->post('/attendance/store', 'EmployeeController@storeAttendance',  ['auth']);

        // Reports
        $router->get('/reports/sales',      'ReportController@salesReport',     ['auth']);
        $router->get('/reports/services',   'ReportController@servicesReport',  ['auth']);
        $router->get('/reports/inventory',  'ReportController@inventoryReport', ['auth']);

        // Settings
        $router->get('/settings',                'SettingController@index',       ['auth']);
        $router->post('/settings/update',        'SettingController@update',      ['auth']);
        $router->post('/settings/upload-logo',   'SettingController@uploadLogo',  ['auth']);
        $router->post('/settings/remove-logo',   'SettingController@removeLogo',  ['auth']);

        // User management
        $router->get('/users',               'SettingController@indexUsers',  ['auth']);
        $router->get('/users/create',        'SettingController@createUser',  ['auth']);
        $router->post('/users/store',        'SettingController@storeUser',   ['auth']);
        $router->post('/users/{id}/update',  'SettingController@updateUser',  ['auth']);
        $router->post('/users/{id}/delete',  'SettingController@deleteUser',  ['auth']);

        return $router;
    }
}
