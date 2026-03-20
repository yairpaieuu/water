<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $db = Database::getInstance();

        // KPI scalars
        $totalCustomers = $this->safeCount($db, 'customers');
        $activeContracts = $this->safeCount($db, 'service_contracts', "`status` = 'active'");
        $pendingJobs    = $this->safeCount($db, 'service_jobs', "`status` = 'pending'");
        $monthlyRevenue = $this->safeSum(
            $db, 'sales', 'total',
            "`status` != 'cancelled' AND `payment_status` = 'paid'"
            . " AND MONTH(`sale_date`) = MONTH(CURDATE()) AND YEAR(`sale_date`) = YEAR(CURDATE())"
        );

        // Low-stock count
        $lowStockCount = $this->safeCount(
            $db, 'inventory_stock', '`quantity` <= `min_quantity` AND `min_quantity` > 0'
        );

        // Last-12-months revenue chart data
        $revenueChart = $this->fetchRevenueChart($db);

        // Services due this week
        $servicesDue = $this->fetchServicesDue($db);

        // Recent jobs (last 5)
        $recentJobs = $this->fetchRecentJobs($db);

        // Recent sales (last 5)
        $recentSales = $this->fetchRecentSales($db);

        $this->render('dashboard.index', [
            'pageTitle'      => 'Dashboard',
            'user'           => Auth::user(),
            'totalCustomers' => $totalCustomers,
            'activeContracts'=> $activeContracts,
            'pendingJobs'    => $pendingJobs,
            'monthlyRevenue' => $monthlyRevenue,
            'revenueChart'   => $revenueChart,
            'lowStockCount'  => $lowStockCount,
            'servicesDue'    => $servicesDue,
            'recentJobs'     => $recentJobs,
            'recentSales'    => $recentSales,
        ]);
    }

    private function safeCount(Database $db, string $table, string $where = ''): int
    {
        try {
            // $where is always a hardcoded internal string – never derived from user input.
            $sql = "SELECT COUNT(*) AS cnt FROM `{$table}`" . ($where ? " WHERE {$where}" : '');
            $row = $db->fetch($sql, []);
            return (int) ($row['cnt'] ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function safeSum(Database $db, string $table, string $column, string $where = ''): float
    {
        try {
            // $where is always a hardcoded internal string – never derived from user input.
            $sql = "SELECT COALESCE(SUM(`{$column}`), 0) AS total FROM `{$table}`"
                 . ($where ? " WHERE {$where}" : '');
            $row = $db->fetch($sql, []);
            return (float) ($row['total'] ?? 0);
        } catch (\Throwable) {
            return 0.0;
        }
    }

    /**
     * Return last 12 months of revenue as [{month, total}, …] for the sparkline chart.
     *
     * @return array<int, array{month: string, total: float}>
     */
    private function fetchRevenueChart(Database $db): array
    {
        try {
            $rows = $db->fetchAll(
                "SELECT DATE_FORMAT(`sale_date`, '%b %Y') AS `month`,
                        COALESCE(SUM(`total`), 0)         AS `total`
                   FROM `sales`
                  WHERE `status` != 'cancelled'
                    AND `sale_date` >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                  GROUP BY YEAR(`sale_date`), MONTH(`sale_date`)
                  ORDER BY YEAR(`sale_date`) ASC, MONTH(`sale_date`) ASC",
                []
            );
            return array_map(
                fn($r) => ['month' => (string)$r['month'], 'total' => (float)$r['total']],
                $rows
            );
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Fetch service jobs scheduled within the next 7 days.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchServicesDue(Database $db): array
    {
        try {
            return $db->fetchAll(
                "SELECT j.id, j.job_type, j.scheduled_date,
                        CONCAT(c.first_name, ' ', c.last_name) AS customer_name
                   FROM `service_jobs` j
              LEFT JOIN `customers` c ON c.id = j.customer_id
                  WHERE j.scheduled_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                    AND j.status NOT IN ('completed','cancelled')
                  ORDER BY j.scheduled_date ASC
                  LIMIT 10",
                []
            );
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Fetch the 5 most recently created service jobs.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchRecentJobs(Database $db): array
    {
        try {
            return $db->fetchAll(
                "SELECT j.id, j.job_type, j.status,
                        CONCAT(c.first_name, ' ', c.last_name) AS customer_name
                   FROM `service_jobs` j
              LEFT JOIN `customers` c ON c.id = j.customer_id
                  ORDER BY j.created_at DESC
                  LIMIT 5",
                []
            );
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Fetch the 5 most recently created sales.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchRecentSales(Database $db): array
    {
        try {
            return $db->fetchAll(
                "SELECT s.id, s.total, s.sale_date AS `date`,
                        CONCAT(c.first_name, ' ', c.last_name) AS customer_name
                   FROM `sales` s
              LEFT JOIN `customers` c ON c.id = s.customer_id
                  WHERE s.status != 'cancelled'
                  ORDER BY s.created_at DESC
                  LIMIT 5",
                []
            );
        } catch (\Throwable) {
            return [];
        }
    }
}
