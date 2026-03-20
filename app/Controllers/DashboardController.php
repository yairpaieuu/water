<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $kpis = $this->fetchKpis();

        $this->render('dashboard.index', [
            'pageTitle'      => 'Dashboard',
            'kpis'           => $kpis,
            'user'           => Auth::user(),
            'recentActivities' => $this->fetchRecentActivities(),
        ]);
    }

    /**
     * Fetch high-level KPI counts from the database.
     * Each query uses a try/catch so a missing table never crashes the dashboard.
     *
     * @return array<string, int|string>
     */
    private function fetchKpis(): array
    {
        $db = Database::getInstance();

        $totalCustomers  = $this->safeCount($db, 'customers');
        $activeServices  = $this->safeCount($db, 'service_contracts', "`status` = 'active'");
        $pendingJobs     = $this->safeCount($db, 'service_jobs', "`status` = 'pending'");
        $monthlyRevenue  = $this->safeSum($db, 'sales', 'total', "`status` != 'cancelled' AND `payment_status` = 'paid' AND MONTH(`sale_date`) = MONTH(CURDATE()) AND YEAR(`sale_date`) = YEAR(CURDATE())");

        return [
            'total_customers' => $totalCustomers,
            'active_services' => $activeServices,
            'pending_jobs'    => $pendingJobs,
            'monthly_revenue' => number_format($monthlyRevenue, 2),
        ];
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
     * Fetch recent activities / audit log entries.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchRecentActivities(): array
    {
        try {
            $db = Database::getInstance();
            return $db->fetchAll(
                "SELECT a.*, u.name AS user_name
                   FROM `activity_log` a
              LEFT JOIN `users` u ON u.id = a.user_id
               ORDER BY a.created_at DESC
                  LIMIT 10",
                []
            );
        } catch (\Throwable) {
            return [];
        }
    }
}
