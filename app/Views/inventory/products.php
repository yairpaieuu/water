<?php
/**
 * Inventory – Products Catalog
 * Variables: $products (array), $csrfField (string)
 */
declare(strict_types=1);

$products = $products ?? [];

$categoryLabels = [
    'purifier'   => 'Purifier',
    'accessory'  => 'Accessory',
    'chemical'   => 'Chemical',
    'spare_part' => 'Spare Part',
];
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Products</h1>
        <p class="text-sm text-slate-500 mt-0.5"><?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> in catalogue</p>
    </div>
    <a href="/inventory/create"
       class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Product
    </a>
</div>

<!-- Edit Product Modal (Alpine.js) -->
<div x-data="productEdit()" x-cloak>
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/50 flex items-center justify-center p-4"
         @click.self="open = false">

        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-lg p-6 relative z-50 max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-slate-800">Edit Product</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" :action="'/inventory/' + productId + '/update'">
                <?= $csrfField ?>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="name" required
                                   class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                            <select name="category" x-model="category"
                                    class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                                <option value="">— Select —</option>
                                <option value="purifier">Purifier</option>
                                <option value="accessory">Accessory</option>
                                <option value="chemical">Chemical</option>
                                <option value="spare_part">Spare Part</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Brand</label>
                            <input type="text" name="brand" x-model="brand"
                                   class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Model Number</label>
                            <input type="text" name="model_number" x-model="modelNumber"
                                   class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select name="status" x-model="status"
                                    class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Purchase Price (LKR)</label>
                            <input type="number" name="purchase_price" x-model="purchasePrice" min="0" step="0.01"
                                   class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Selling Price (LKR) <span class="text-red-500">*</span></label>
                            <input type="number" name="selling_price" x-model="sellingPrice" min="0" step="0.01" required
                                   class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                            <textarea name="description" x-model="description" rows="2"
                                      class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Changes
                    </button>
                    <button type="button" @click="open = false"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <?php if (empty($products)): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center px-4">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <h3 class="text-base font-semibold text-slate-700 mb-1">No products yet</h3>
            <p class="text-sm text-slate-500 mb-4">Add your first product to the catalogue.</p>
            <a href="/inventory/create"
               class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Product
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">Code</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600">Name</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden md:table-cell">Category</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 hidden lg:table-cell">Brand</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600 hidden sm:table-cell">Purchase</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Selling</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($products as $prod): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-sky-600 font-medium whitespace-nowrap">
                            <?= e($prod['product_code'] ?? '') ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800"><?= e($prod['name'] ?? '') ?></div>
                            <?php if (!empty($prod['model_number'])): ?>
                            <div class="text-xs text-slate-400"><?= e($prod['model_number']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="capitalize text-slate-600">
                                <?= e($categoryLabels[$prod['category'] ?? ''] ?? ucfirst(str_replace('_', ' ', $prod['category'] ?? ''))) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-slate-600">
                            <?= e($prod['brand'] ?? '—') ?>
                        </td>
                        <td class="px-4 py-3 text-right text-slate-600 hidden sm:table-cell">
                            <?= number_format((float)($prod['purchase_price'] ?? 0), 2) ?>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-slate-800">
                            <?= number_format((float)($prod['selling_price'] ?? 0), 2) ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if (($prod['status'] ?? '') === 'active'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">Active</span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <button type="button"
                                    @click="openEdit(
                                        <?= e((string)$prod['id']) ?>,
                                        '<?= e(addslashes($prod['name'] ?? '')) ?>',
                                        '<?= e(addslashes($prod['category'] ?? '')) ?>',
                                        '<?= e(addslashes($prod['brand'] ?? '')) ?>',
                                        '<?= e(addslashes($prod['model_number'] ?? '')) ?>',
                                        '<?= e(addslashes($prod['description'] ?? '')) ?>',
                                        <?= e((string)(float)($prod['purchase_price'] ?? 0)) ?>,
                                        <?= e((string)(float)($prod['selling_price'] ?? 0)) ?>,
                                        '<?= e($prod['status'] ?? 'active') ?>'
                                    )"
                                    class="inline-flex items-center gap-1 text-xs text-slate-600 hover:text-sky-600 font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function productEdit() {
    return {
        open: false,
        productId: 0,
        name: '',
        category: '',
        brand: '',
        modelNumber: '',
        description: '',
        purchasePrice: 0,
        sellingPrice: 0,
        status: 'active',
        openEdit(id, name, category, brand, modelNumber, description, purchasePrice, sellingPrice, status) {
            this.productId    = id;
            this.name         = name;
            this.category     = category;
            this.brand        = brand;
            this.modelNumber  = modelNumber;
            this.description  = description;
            this.purchasePrice = purchasePrice;
            this.sellingPrice  = sellingPrice;
            this.status       = status;
            this.open         = true;
        },
    };
}
</script>
