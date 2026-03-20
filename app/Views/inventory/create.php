<?php
/**
 * Inventory – Create
 * Variables: $branches (array), $errors (array), $old (array)
 */
declare(strict_types=1);

$pageTitle = 'Add Product';
$errors    = $errors ?? [];
$old       = $old    ?? [];
$branches  = $branches ?? [];

$v   = fn(string $k): string => e($old[$k] ?? '');
$err = fn(string $k): string => $errors[$k] ?? '';
?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="/inventory"
           class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add Product</h1>
            <p class="text-sm text-slate-500 mt-0.5">Fill in product details and set initial stock levels.</p>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
<div class="flex items-start gap-3 p-4 mb-6 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>
        <p class="font-medium mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <?php foreach ($errors as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<form method="POST" action="/inventory/store">
    <?= \App\Core\CSRF::field() ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <!-- LEFT: Product details -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Basic Info -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-700 mb-4">Product Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Product Code -->
                    <div>
                        <label for="product_code" class="block text-sm font-medium text-slate-700 mb-1">
                            Product Code <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="product_code" name="product_code"
                                   value="<?= $v('product_code') ?>"
                                   placeholder="PROD-0001"
                                   class="flex-1 block border <?= $err('product_code') ? 'border-red-400' : 'border-slate-300' ?> rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-slate-50">
                            <button type="button" id="genCodeBtn"
                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-lg border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 text-xs font-medium transition-colors whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Generate
                            </button>
                        </div>
                        <?php if ($err('product_code')): ?><p class="mt-1 text-xs text-red-600"><?= e($err('product_code')) ?></p><?php endif; ?>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select id="status" name="status"
                                class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="active"   <?= ($old['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <!-- Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="<?= $v('name') ?>"
                               placeholder="e.g. RO Water Purifier 7-Stage"
                               class="block w-full border <?= $err('name') ? 'border-red-400' : 'border-slate-300' ?> rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <?php if ($err('name')): ?><p class="mt-1 text-xs text-red-600"><?= e($err('name')) ?></p><?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Product description…"
                                  class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 resize-none"><?= $v('description') ?></textarea>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category"
                                class="block w-full border <?= $err('category') ? 'border-red-400' : 'border-slate-300' ?> rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">— Select Category —</option>
                            <?php foreach (['purifier' => 'Purifier', 'accessory' => 'Accessory', 'chemical' => 'Chemical', 'spare_part' => 'Spare Part'] as $val => $label): ?>
                            <option value="<?= e($val) ?>" <?= ($old['category'] ?? '') === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($err('category')): ?><p class="mt-1 text-xs text-red-600"><?= e($err('category')) ?></p><?php endif; ?>
                    </div>

                    <!-- Brand -->
                    <div>
                        <label for="brand" class="block text-sm font-medium text-slate-700 mb-1">Brand</label>
                        <input type="text" id="brand" name="brand"
                               value="<?= $v('brand') ?>"
                               placeholder="e.g. AquaPure"
                               class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <!-- Model Number -->
                    <div>
                        <label for="model_number" class="block text-sm font-medium text-slate-700 mb-1">Model Number</label>
                        <input type="text" id="model_number" name="model_number"
                               value="<?= $v('model_number') ?>"
                               placeholder="e.g. AP-RO7-2024"
                               class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>

                    <!-- Service Interval -->
                    <div>
                        <label for="service_interval_months" class="block text-sm font-medium text-slate-700 mb-1">
                            Service Interval
                            <span class="text-slate-400 font-normal">(months)</span>
                        </label>
                        <input type="number" id="service_interval_months" name="service_interval_months"
                               value="<?= $v('service_interval_months') ?>"
                               min="1" placeholder="e.g. 6"
                               class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-700 mb-4">Pricing</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="purchase_price" class="block text-sm font-medium text-slate-700 mb-1">
                            Purchase Price (LKR)
                        </label>
                        <input type="number" id="purchase_price" name="purchase_price"
                               value="<?= $v('purchase_price') ?>"
                               min="0" step="0.01" placeholder="0.00"
                               class="block w-full border <?= $err('purchase_price') ? 'border-red-400' : 'border-slate-300' ?> rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <?php if ($err('purchase_price')): ?><p class="mt-1 text-xs text-red-600"><?= e($err('purchase_price')) ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="selling_price" class="block text-sm font-medium text-slate-700 mb-1">
                            Selling Price (LKR) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="selling_price" name="selling_price"
                               value="<?= $v('selling_price') ?>"
                               min="0" step="0.01" placeholder="0.00"
                               class="block w-full border <?= $err('selling_price') ? 'border-red-400' : 'border-slate-300' ?> rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <?php if ($err('selling_price')): ?><p class="mt-1 text-xs text-red-600"><?= e($err('selling_price')) ?></p><?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Initial Stock per branch -->
            <?php if (!empty($branches)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-semibold text-slate-700 mb-1">Initial Stock</h2>
                <p class="text-sm text-slate-500 mb-4">Set opening stock quantities and minimum stock levels per branch.</p>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="text-left px-4 py-2.5 font-semibold text-slate-600">Branch</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 w-36">Initial Qty</th>
                                <th class="text-center px-4 py-2.5 font-semibold text-slate-600 w-36">Min Stock Level</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($branches as $b): ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-700"><?= e($b['name'] ?? '') ?></div>
                                    <?php if (!empty($b['city'])): ?>
                                    <div class="text-xs text-slate-400"><?= e($b['city']) ?></div>
                                    <?php endif; ?>
                                    <input type="hidden" name="branch_ids[]" value="<?= e($b['id']) ?>">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number"
                                           name="initial_stock[<?= e($b['id']) ?>]"
                                           value="<?= e((string)(int)($old['initial_stock'][$b['id']] ?? 0)) ?>"
                                           min="0" step="1"
                                           class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-sky-500">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number"
                                           name="min_stock[<?= e($b['id']) ?>]"
                                           value="<?= e((string)(int)($old['min_stock'][$b['id']] ?? 5)) ?>"
                                           min="0" step="1"
                                           class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-sky-500">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: Actions -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sticky top-20">
                <h2 class="text-base font-semibold text-slate-700 mb-4">Save Product</h2>
                <p class="text-sm text-slate-500 mb-6">
                    Review all fields before saving. Stock records will be created for each branch with the quantities specified.
                </p>
                <div class="space-y-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Product
                    </button>
                    <a href="/inventory"
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('genCodeBtn').addEventListener('click', function () {
    const btn  = this;
    const input = document.getElementById('product_code');
    btn.disabled = true;
    btn.textContent = '…';

    fetch('/inventory/generate-code', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(d => { if (d.code) { input.value = d.code; } })
        .catch(() => {})
        .finally(() => { btn.disabled = false; btn.innerHTML = '<svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Generate'; });
});
</script>
