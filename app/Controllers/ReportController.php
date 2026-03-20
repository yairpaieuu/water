<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Models\Inventory;
use App\Models\Sale;

class ReportController extends BaseController
{
    public function salesReport(): void
    {
        $request = new Request();
        $year    = (int) $request->get('year', (int) date('Y'));

        $saleModel      = new Sale();
        $monthlyRevenue = $saleModel->getMonthlyRevenue($year);
        $db             = Database::getInstance();

        $topProducts = $db->fetchAll(
            "SELECT p.`name` AS product_name, p.`product_code`,
                    SUM(si.`quantity`) AS total_qty,
                    SUM(si.`total`)    AS total_revenue
               FROM `sale_items` si
               JOIN `products` p ON p.`id` = si.`product_id`
               JOIN `sales`    s ON s.`id` = si.`sale_id`
              WHERE YEAR(s.`sale_date`) = ?
                AND s.`status` != 'cancelled'
           GROUP BY si.`product_id`, p.`name`, p.`product_code`
           ORDER BY total_revenue DESC
              LIMIT 10",
            [$year]
        );

        $salesByStatus = $db->fetchAll(
            "SELECT `status`, COUNT(*) AS cnt, SUM(`total`) AS revenue
               FROM `sales`
              WHERE YEAR(`sale_date`) = ?
           GROUP BY `status`",
            [$year]
        );

        $salesByBranch = $db->fetchAll(
            "SELECT b.`name` AS branch_name,
                    COUNT(s.`id`) AS cnt,
                    COALESCE(SUM(s.`total`), 0) AS revenue
               FROM `sales` s
          LEFT JOIN `branches` b ON b.`id` = s.`branch_id`
              WHERE YEAR(s.`sale_date`) = ?
                AND s.`status` != 'cancelled'
           GROUP BY s.`branch_id`, b.`name`
           ORDER BY revenue DESC",
            [$year]
        );

        $totalRevenue = array_sum($monthlyRevenue);

        $this->render('reports.sales', [
            'pageTitle'     => 'Sales Report',
            'year'          => $year,
            'monthlyRevenue'=> $monthlyRevenue,
            'topProducts'   => $topProducts,
            'salesByStatus' => $salesByStatus,
            'salesByBranch' => $salesByBranch,
            'totalRevenue'  => $totalRevenue,
            'user'          => Auth::user(),
            'error'         => Session::getFlash('error'),
        ]);
    }

    public function servicesReport(): void
    {
        $request  = new Request();
        $month    = (int) $request->get('month', (int) date('m'));
        $year     = (int) $request->get('year',  (int) date('Y'));

        $db = Database::getInstance();

        $jobsByStatus = $db->fetchAll(
            "SELECT `status`, COUNT(*) AS cnt
               FROM `service_jobs`
              WHERE MONTH(`created_at`) = ? AND YEAR(`created_at`) = ?
           GROUP BY `status`",
            [$month, $year]
        );

        $jobsByBranch = $db->fetchAll(
            "SELECT b.`name` AS branch_name, COUNT(sj.`id`) AS cnt
               FROM `service_jobs` sj
          LEFT JOIN `branches` b ON b.`id` = sj.`branch_id`
              WHERE MONTH(sj.`created_at`) = ? AND YEAR(sj.`created_at`) = ?
           GROUP BY sj.`branch_id`, b.`name`
           ORDER BY cnt DESC",
            [$month, $year]
        );

        $contractsByStatus = $db->fetchAll(
            "SELECT `status`, COUNT(*) AS cnt
               FROM `service_contracts`
           GROUP BY `status`"
        );

        $upcomingServices = $db->fetchAll(
            "SELECT sc.*,
                    CONCAT(c.`first_name`, ' ', c.`last_name`) AS customer_name,
                    c.`phone` AS customer_phone,
                    p.`name`  AS product_name,
                    b.`name`  AS branch_name
               FROM `service_contracts` sc
               JOIN `customers` c ON c.`id` = sc.`customer_id`
          LEFT JOIN `products`  p ON p.`id` = sc.`product_id`
          LEFT JOIN `branches`  b ON b.`id` = sc.`branch_id`
              WHERE sc.`status` = 'active'
                AND sc.`next_service_date` BETWEEN CURDATE()
                    AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
           ORDER BY sc.`next_service_date` ASC
              LIMIT 20"
        );

        $this->render('reports.services', [
            'pageTitle'         => 'Services Report',
            'month'             => $month,
            'year'              => $year,
            'jobsByStatus'      => $jobsByStatus,
            'jobsByBranch'      => $jobsByBranch,
            'contractsByStatus' => $contractsByStatus,
            'upcomingServices'  => $upcomingServices,
            'user'              => Auth::user(),
            'error'             => Session::getFlash('error'),
        ]);
    }

    public function inventoryReport(): void
    {
        $inventory = new Inventory();
        $db        = Database::getInstance();

        $lowStock = $inventory->getLowStock();

        $stockByBranch = $db->fetchAll(
            "SELECT b.`name` AS branch_name,
                    COUNT(s.`product_id`) AS product_count,
                    SUM(s.`quantity`) AS total_qty,
                    SUM(s.`quantity` * p.`purchase_price`) AS stock_value
               FROM `inventory_stock` s
               JOIN `branches`  b ON b.`id` = s.`branch_id`
               JOIN `products`  p ON p.`id` = s.`product_id`
           GROUP BY s.`branch_id`, b.`name`
           ORDER BY stock_value DESC"
        );

        $stockByCategory = $db->fetchAll(
            "SELECT p.`category`,
                    COUNT(DISTINCT s.`product_id`) AS product_count,
                    SUM(s.`quantity`) AS total_qty
               FROM `inventory_stock` s
               JOIN `products` p ON p.`id` = s.`product_id`
           GROUP BY p.`category`
           ORDER BY total_qty DESC"
        );

        $allStock = $db->fetchAll(
            "SELECT s.*, p.`name` AS product_name, p.`product_code`, p.`category`,
                    b.`name` AS branch_name
               FROM `inventory_stock` s
               JOIN `products`  p ON p.`id` = s.`product_id`
               JOIN `branches`  b ON b.`id` = s.`branch_id`
           ORDER BY b.`name` ASC, p.`name` ASC"
        );

        $this->render('reports.inventory', [
            'pageTitle'       => 'Inventory Report',
            'lowStock'        => $lowStock,
            'stockByBranch'   => $stockByBranch,
            'stockByCategory' => $stockByCategory,
            'allStock'        => $allStock,
            'user'            => Auth::user(),
            'error'           => Session::getFlash('error'),
        ]);
    }
}
