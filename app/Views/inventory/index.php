<?php
/**
 * Inventory – Index
 * Variables: $products (array with stock), $branches (array), $branchFilter (int), $lowStockItems (array)
 */
declare(strict_types=1);

$pageTitle     = 'Inventory';
$products      = $products      ?? [];
$branches      = $branches      ?? [];
$branchFilter  = (int)($branchFilter ?? 0);
$lowStockItems = $lowStockItems ?? [];

function stockStatusBadge(int $qty, int $min): string
{
    if ($qty <= 0)        { return 'bg-red-100 text-red-700 border border-red-200'; }
    if ($qty <= $min)     { return 'bg-amber-100 text-amber-700 border border-amber-200'; }
    return 'bg-green-100 text-green-700 border border-green-200';
}

function stockStatusLabel(int $qty, int $min): string
{
    if ($qty <= 0)    { return 'Out of Stock'; }
    if ($qty <= $min) { return 'Low Stock'; }
    return 'In Stock';
}
?>

<!-- Low stock alert banner -->
<?php if (!empty($lowStockItems)): ?>
<div class="flex items-start gap-3 p-4 mb-6 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div class="flex-1 min-w-0">
        <p class="font-semibold mb-1">Low Stock Alert – <?= count($lowStockItems) ?> product<?= count($lowStockItems) !== 1 ? 's' : '' ?> need attention</p>
        <div class="flex flex-wrap gap-2 mt-1">
            <?php foreach ($lowStockItems as $ls): ?>
            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 border border-red-200 rounded-full px-2.5 py-0.5 text-xs font-medium">
                <?= e($ls['name'] ?? '') ?>
                <span class="opacity-70">(<?= e((string)(int)($ls['quantity'] ?? 0)) ?> left)</span>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Page header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Inventory</h1>
        <p class="text-sm text-slate-500 mt-0.5"><?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?></p>
    </div>
    <a href="/inventory/create"
       class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Product
    </a>
</div>

<!-- Branch filter tabs -->
<div class="flex items-center gap-1 mb-6 overflow-x-auto pb-1">
    <a href="/inventory"
       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $branchFilter === 0 ? 'bg-sky-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
        All Branches
    </a>
    <?php foreach ($branches as $b): ?>
    <a href="/inventory?branch_id=<?= e($b['id']) ?>"
       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $branchFilter === (int)$b['id'] ? 'bg-sky-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
        <?= e($b['name']) ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- Stock adjustment modal (Alpine.js) -->
<div x-data="stockAdjust()" x-cloak>
    <!-- Modal overlay -->
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
             class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-md p-6 relative z-50">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-slate-800">Adjust Stock</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <p class="text-sm text-slate-600 mb-4">
                Product: <strong x-text="productName"></strong><br>
                Current stock: <strong x-text="currentQty"></strong>
            </p>

            <form method="POST" action="/stock/adjust" @submit="open = false">
                <?= \App\Core\CSRF::field() ?>
                <input type="hidden" name="product_id" :value="productId">
                <input type="hidden" name="branch_id"  :value="branchId">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Branch</label>
                        <select name="branch_id" x-model="branchId"
                                class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <?php foreach ($branches as $b): ?>
                            <option value="<?= e($b['id']) ?>"><?= e($b['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            Quantity Change
                            <span class="text-slate-400 font-normal">(use negative to reduce)</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="qty = qty - 1"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-lg font-bold transition-colors">−</button>
                            <input type="number" name="quantity_change" x-model.number="qty" step="1"
                                   class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm text-center focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <button type="button" @click="qty = qty + 1"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-lg font-bold transition-colors">+</button>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            New quantity: <strong x-text="Math.max(0, currentQty + qty)"></strong>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Reason</label>
                        <input type="text" name="reason" placeholder="e.g. Stock received, damaged…"
                               class="block w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Apply
                    </button>
                    <button type="button" @click="open = false"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <?php if (empty($products)): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center px-4">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <h3 class="text-base font-semibold text-slate-700 mb-1">No products found</h3>
            <p class="text-sm text-slate-500 mb-4">Add products to start tracking inventory.</p>
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
                        <?php foreach ($branches as $b): ?>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600 hidden lg:table-cell whitespace-nowrap">
                            <?= e($b['name']) ?>
                        </th>
                        <?php endforeach; ?>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Total</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600 hidden sm:table-cell">Min</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600">Status</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($products as $prod):
                        $totalStock = 0;
                        $branchStocks = [];
                        foreach ($branches as $b) {
                            $qty = (int)($prod['branch_stock'][(int)$b['id']] ?? 0);
                            $branchStocks[(int)$b['id']] = $qty;
                            $totalStock += $qty;
                        }
                        $minStock = (int)($prod['min_quantity'] ?? 0);
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-sky-600 font-medium whitespace-nowrap">
                            <?= e($prod['product_code'] ?? '') ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800"><?= e($prod['name'] ?? '') ?></div>
                            <?php if (!empty($prod['brand'])): ?>
                            <div class="text-xs text-slate-400"><?= e($prod['brand']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="capitalize text-slate-600"><?= e(str_replace('_', ' ', $prod['category'] ?? '')) ?></span>
                        </td>
                        <?php foreach ($branches as $b): ?>
                        <td class="px-4 py-3 text-center hidden lg:table-cell">
                            <span class="font-medium text-slate-800"><?= e((string)($branchStocks[(int)$b['id']] ?? 0)) ?></span>
                        </td>
                        <?php endforeach; ?>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold text-slate-800"><?= e((string)$totalStock) ?></span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-500 hidden sm:table-cell"><?= e((string)$minStock) ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= stockStatusBadge($totalStock, $minStock) ?>">
                                <?= stockStatusLabel($totalStock, $minStock) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <button type="button"
                                    @click="openModal(<?= e((string)$prod['id']) ?>, '<?= e(addslashes($prod['name'] ?? '')) ?>', <?= e((string)$totalStock) ?>, <?= e((string)($branchFilter ?: ($branches[0]['id'] ?? 0))) ?>)"
                                    class="inline-flex items-center gap-1 text-xs text-slate-600 hover:text-sky-600 font-medium mr-3 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Adjust
                            </button>
                            <a href="/inventory/<?= e($prod['id']) ?>"
                               class="inline-flex items-center gap-1 text-xs text-sky-600 hover:text-sky-800 font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </a>
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
function stockAdjust() {
    return {
        open: false,
        productId: 0,
        productName: '',
        currentQty: 0,
        branchId: 0,
        qty: 0,
        openModal(productId, productName, currentQty, branchId) {
            this.productId   = productId;
            this.productName = productName;
            this.currentQty  = currentQty;
            this.branchId    = branchId;
            this.qty         = 0;
            this.open        = true;
        },
    };
}
</script>
