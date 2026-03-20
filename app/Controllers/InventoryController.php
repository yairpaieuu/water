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
        $products = (new Product())->findAll([], 'name ASC');

        $this->render('inventory.products', [
            'pageTitle' => 'Products',
            'products'  => $products,
            'csrfField' => CSRF::field(),
            'user'      => Auth::user(),
            'success'   => Session::getFlash('success'),
            'error'     => Session::getFlash('error'),
        ]);
    }

    public function stockIndex(): void
    {
        $request      = new Request();
        $branchFilter = (int) $request->get('branch_id', 0);

        $db        = Database::getInstance();
        $inventory = new Inventory();
        $branches  = (new Branch())->getActive();

        // All active products
        $allProducts = $db->fetchAll(
            "SELECT p.*, COALESCE(MIN(s.`min_quantity`), 0) AS min_quantity
               FROM `products` p
               LEFT JOIN `inventory_stock` s ON s.`product_id` = p.`id`
              WHERE p.`status` = 'active'
              GROUP BY p.`id`
              ORDER BY p.`name` ASC"
        );

        // All stock rows
        $stockRows = $db->fetchAll(
            "SELECT `product_id`, `branch_id`, `quantity` FROM `inventory_stock`"
        );

        // Index stock as [product_id][branch_id] => quantity
        $stockMap = [];
        foreach ($stockRows as $row) {
            $stockMap[(int) $row['product_id']][(int) $row['branch_id']] = (int) $row['quantity'];
        }

        // Attach branch_stock sub-array to every product
        $products = [];
        foreach ($allProducts as $prod) {
            $pid = (int) $prod['id'];
            if ($branchFilter > 0) {
                $prod['branch_stock'] = [$branchFilter => ($stockMap[$pid][$branchFilter] ?? 0)];
            } else {
                $prod['branch_stock'] = $stockMap[$pid] ?? [];
            }
            $products[] = $prod;
        }

        $this->render('inventory.index', [
            'pageTitle'     => 'Stock',
            'products'      => $products,
            'branches'      => $branches,
            'branchFilter'  => $branchFilter,
            'lowStockItems' => $inventory->getLowStock(),
            'user'          => Auth::user(),
            'success'       => Session::getFlash('success'),
            'error'         => Session::getFlash('error'),
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
