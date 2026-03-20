<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Models\Branch;
use App\Models\Sale;

class InvoiceController extends BaseController
{
    public function index(): void
    {
        $request  = new Request();
        $status   = $request->get('status', '');
        $branchId = (int) $request->get('branch_id', 0);
        $from     = $request->get('from', '');
        $to       = $request->get('to', '');

        $db     = Database::getInstance();
        $where  = ["s.`status` IN ('confirmed','delivered')"];
        $params = [];

        if ($status !== '' && in_array($status, ['confirmed', 'delivered'], true)) {
            $where[]  = 's.`status` = ?';
            $params[] = $status;
        }
        if ($branchId > 0) {
            $where[]  = 's.`branch_id` = ?';
            $params[] = $branchId;
        }
        if ($from !== '') {
            $where[]  = 's.`sale_date` >= ?';
            $params[] = $from;
        }
        if ($to !== '') {
            $where[]  = 's.`sale_date` <= ?';
            $params[] = $to;
        }

        $whereSql = ' WHERE ' . implode(' AND ', $where);
        $invoices = $db->fetchAll(
            "SELECT s.*,
                    CONCAT(c.`first_name`, ' ', c.`last_name`) AS customer_name,
                    b.`name` AS branch_name
               FROM `sales` s
               JOIN `customers` c ON c.`id` = s.`customer_id`
          LEFT JOIN `branches`  b ON b.`id` = s.`branch_id`"
            . $whereSql
            . " ORDER BY s.`sale_date` DESC, s.`id` DESC",
            $params
        );

        $this->render('invoices.index', [
            'pageTitle' => 'Invoices',
            'invoices'  => $invoices,
            'branches'  => (new Branch())->getActive(),
            'status'    => $status,
            'branchId'  => $branchId,
            'from'      => $from,
            'to'        => $to,
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function show(string $id): void
    {
        $saleId = (int) $id;
        $sale   = (new Sale())->getWithCustomer($saleId);

        if ($sale === false || !in_array($sale['status'] ?? '', ['confirmed', 'delivered'], true)) {
            $this->error('Invoice not found.', '/invoices');
        }

        $items = (new Sale())->getItems($saleId);

        $this->render('invoices.show', [
            'pageTitle' => 'Invoice ' . ($sale['sale_code'] ?? ''),
            'sale'      => $sale,
            'items'     => $items,
            'user'      => Auth::user(),
        ]);
    }
}
