<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;

class SalesController extends BaseController
{
    public function index(): void
    {
        $request  = new Request();
        $status   = $request->get('status', '');
        $branchId = (int) $request->get('branch_id', 0);
        $from     = $request->get('from', '');
        $to       = $request->get('to', '');

        $db     = Database::getInstance();
        $where  = [];
        $params = [];

        if ($status !== '') {
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

        $whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
        $orders   = $db->fetchAll(
            "SELECT s.*,
                    CONCAT(c.`first_name`, ' ', c.`last_name`) AS customer_name,
                    b.`name` AS branch_name
               FROM `sales` s
               JOIN `customers` c ON c.`id` = s.`customer_id`
          LEFT JOIN `branches`  b ON b.`id` = s.`branch_id`"
            . $whereSql
            . " ORDER BY s.`created_at` DESC",
            $params
        );

        $this->render('sales.index', [
            'pageTitle' => 'Sales Orders',
            'orders'    => $orders,
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

    public function create(): void
    {
        $this->render('sales.create', [
            'pageTitle' => 'New Order',
            'customers' => Database::getInstance()->fetchAll(
                "SELECT id, customer_code, first_name, last_name FROM `customers`
                  WHERE `status` = 'active' ORDER BY `first_name` ASC, `last_name` ASC"
            ),
            'products'  => (new Product())->getActive(),
            'branches'  => (new Branch())->getActive(),
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function store(): void
    {
        $request = new Request();
        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $customerId = (int) $request->post('customer_id', 0);
        $branchId   = (int) $request->post('branch_id', 0);
        $saleDate   = $request->post('sale_date', date('Y-m-d'));
        $discount   = (float) $request->post('discount', 0);
        $tax        = (float) $request->post('tax', 0);
        $notes      = $request->post('notes', '');

        $validator = new Validator();
        $errors    = $validator->validate(
            ['customer_id' => (string) $customerId, 'sale_date' => $saleDate],
            ['customer_id' => 'required|numeric', 'sale_date' => 'required']
        );

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        // Collect line items
        $productIds  = $_POST['product_id']  ?? [];
        $quantities  = $_POST['quantity']    ?? [];
        $unitPrices  = $_POST['unit_price']  ?? [];
        $itemDiscs   = $_POST['item_discount'] ?? [];

        if (empty($productIds)) {
            Session::flash('error', 'At least one product line item is required.');
            $this->back();
        }

        $items    = [];
        $subtotal = 0.0;

        foreach ($productIds as $i => $productId) {
            $productId = (int) $productId;
            $qty       = max(1, (int) ($quantities[$i] ?? 1));
            $price     = (float) ($unitPrices[$i] ?? 0);
            $itemDisc  = (float) ($itemDiscs[$i]  ?? 0);
            $lineTotal = ($price * $qty) - $itemDisc;
            $subtotal += $lineTotal;

            $items[] = [
                'product_id'  => $productId,
                'quantity'    => $qty,
                'unit_price'  => $price,
                'discount'    => $itemDisc,
                'total'       => $lineTotal,
            ];
        }

        $grandTotal = $subtotal - $discount + $tax;

        $saleModel = new Sale();
        $saleData  = [
            'sale_code'      => $saleModel->generateCode(),
            'customer_id'    => $customerId,
            'branch_id'      => $branchId > 0 ? $branchId : null,
            'sale_date'      => $saleDate,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'tax'            => $tax,
            'total'          => $grandTotal,
            'status'         => 'quotation',
            'payment_status' => 'pending',
            'notes'          => $notes,
            'created_by'     => Auth::id(),
        ];

        $id = $saleModel->createWithItems($saleData, $items);
        if ($id === false) {
            $this->error('Failed to create order. Please try again.');
        }

        $this->success('Order created successfully.', "/orders/{$id}");
    }

    public function show(string $id): void
    {
        $saleId = (int) $id;
        $sale   = (new Sale())->getWithCustomer($saleId);

        if ($sale === false) {
            $this->error('Order not found.', '/orders');
        }

        $items = (new Sale())->getItems($saleId);

        $this->render('sales.show', [
            'pageTitle' => $sale['sale_code'],
            'sale'      => $sale,
            'items'     => $items,
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function update(string $id): void
    {
        $saleId  = (int) $id;
        $request = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $sale = (new Sale())->find($saleId);
        if ($sale === false) {
            $this->error('Order not found.', '/orders');
        }

        $allowedStatuses        = ['quotation', 'confirmed', 'delivered', 'cancelled'];
        $allowedPaymentStatuses = ['pending', 'partial', 'paid'];

        $status        = $request->post('status', $sale['status']);
        $paymentStatus = $request->post('payment_status', $sale['payment_status']);
        $notes         = $request->post('notes', $sale['notes'] ?? '');

        if (!in_array($status, $allowedStatuses, true)) {
            $this->error('Invalid order status.', "/orders/{$saleId}");
        }
        if (!in_array($paymentStatus, $allowedPaymentStatuses, true)) {
            $this->error('Invalid payment status.', "/orders/{$saleId}");
        }

        (new Sale())->update($saleId, [
            'status'         => $status,
            'payment_status' => $paymentStatus,
            'notes'          => $notes,
        ]);

        $this->success('Order updated successfully.', "/orders/{$saleId}");
    }

    public function delete(string $id): void
    {
        $saleId  = (int) $id;
        $request = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $sale = (new Sale())->find($saleId);
        if ($sale === false) {
            $this->error('Order not found.', '/orders');
        }

        if (($sale['status'] ?? '') !== 'quotation') {
            $this->error('Only quotation-status orders can be deleted.', "/orders/{$saleId}");
        }

        (new Sale())->delete($saleId);
        $this->success('Order deleted successfully.', '/orders');
    }
}
