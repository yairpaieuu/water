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
use App\Models\Inventory;
use App\Models\Product;

class InventoryController extends BaseController
{
    public function index(): void
    {
        $request  = new Request();
        $branchId = (int) $request->get('branch_id', 0);

        $db       = Database::getInstance();
        $inventory = new Inventory();

        if ($branchId > 0) {
            $stock = $inventory->getByBranch($branchId);
        } else {
            $stock = $db->fetchAll(
                "SELECT s.*, p.`name` AS product_name, p.`product_code`, p.`category`,
                        p.`brand`, p.`model_number`, b.`name` AS branch_name
                   FROM `inventory_stock` s
                   JOIN `products`  p ON p.`id` = s.`product_id`
                   JOIN `branches`  b ON b.`id` = s.`branch_id`
               ORDER BY b.`name` ASC, p.`name` ASC"
            );
        }

        $this->render('inventory.index', [
            'pageTitle' => 'Inventory',
            'stock'     => $stock,
            'branches'  => (new Branch())->getActive(),
            'branchId'  => $branchId,
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function create(): void
    {
        $this->render('inventory.create', [
            'pageTitle' => 'Add Product',
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

        $data = [
            'name'          => $request->post('name', ''),
            'category'      => $request->post('category', ''),
            'brand'         => $request->post('brand', ''),
            'model_number'  => $request->post('model_number', ''),
            'description'   => $request->post('description', ''),
            'selling_price' => (float) $request->post('selling_price', 0),
            'purchase_price'=> (float) $request->post('purchase_price', 0),
            'status'        => 'active',
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'name'          => 'required|max:200',
            'selling_price' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        $productModel          = new Product();
        $data['product_code']  = $productModel->generateCode();

        $id = $productModel->create($data);
        if ($id === false) {
            $this->error('Failed to create product. Please try again.');
        }

        $this->success('Product created successfully.', '/inventory');
    }

    public function update(string $id): void
    {
        $productId = (int) $id;
        $request   = new Request();

        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            $this->error('Invalid request');
        }

        $product = (new Product())->find($productId);
        if ($product === false) {
            $this->error('Product not found.', '/inventory');
        }

        $data = [
            'name'          => $request->post('name', ''),
            'category'      => $request->post('category', ''),
            'brand'         => $request->post('brand', ''),
            'model_number'  => $request->post('model_number', ''),
            'description'   => $request->post('description', ''),
            'selling_price' => (float) $request->post('selling_price', 0),
            'purchase_price'=> (float) $request->post('purchase_price', 0),
            'status'        => $request->post('status', 'active'),
        ];

        $validator = new Validator();
        $errors    = $validator->validate($data, [
            'name'          => 'required|max:200',
            'selling_price' => 'required|numeric',
        ]);

        if (!empty($errors)) {
            Session::flash('error', reset($errors));
            $this->back();
        }

        (new Product())->update($productId, $data);
        $this->success('Product updated successfully.', '/inventory');
    }

    public function adjustStock(): void
    {
        $request = new Request();
        if (!CSRF::verify($request->post('_csrf_token', ''))) {
            if ($request->isAjax()) {
                $this->json(['error' => 'Invalid request'], 403);
            }
            $this->error('Invalid request');
        }

        $productId      = (int) $request->post('product_id', 0);
        $branchId       = (int) $request->post('branch_id', 0);
        $quantityChange = (int) $request->post('quantity_change', 0);
        $reason         = $request->post('reason', '');

        $validator = new Validator();
        $errors    = $validator->validate(
            [
                'product_id'      => (string) $productId,
                'branch_id'       => (string) $branchId,
                'quantity_change' => (string) $quantityChange,
            ],
            [
                'product_id'      => 'required|numeric',
                'branch_id'       => 'required|numeric',
                'quantity_change' => 'required|numeric',
            ]
        );

        if (!empty($errors)) {
            if ($request->isAjax()) {
                $this->json(['error' => reset($errors)], 422);
            }
            Session::flash('error', reset($errors));
            $this->back();
        }

        $success = (new Inventory())->adjustStock($productId, $branchId, $quantityChange);

        if (!$success) {
            if ($request->isAjax()) {
                $this->json(['error' => 'Stock adjustment failed.'], 500);
            }
            $this->error('Stock adjustment failed. Please try again.');
        }

        if ($request->isAjax()) {
            $row = Database::getInstance()->fetch(
                "SELECT `quantity` FROM `inventory_stock`
                  WHERE `product_id` = ? AND `branch_id` = ? LIMIT 1",
                [$productId, $branchId]
            );
            $this->json(['success' => true, 'new_quantity' => (int) ($row['quantity'] ?? 0)]);
        }

        $this->success('Stock adjusted successfully.', '/inventory');
    }
}
