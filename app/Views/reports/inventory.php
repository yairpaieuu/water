<?php $pageTitle = 'Inventory Report'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="space-y-6">
  <h2 class="text-xl font-bold text-slate-800">Inventory Report</h2>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Total Products</p>
      <p class="text-2xl font-bold text-slate-800 mt-1"><?= number_format((int)($totalProducts ?? 0)) ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Total Stock Value</p>
      <p class="text-2xl font-bold text-sky-600 mt-1"><?= number_format((float)($totalValue ?? 0), 2) ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Low Stock Items</p>
      <p class="text-2xl font-bold text-red-500 mt-1"><?= number_format(count($lowStockItems ?? [])) ?></p>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
      <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <h3 class="font-semibold text-slate-800">Low Stock Items</h3>
    </div>
    <?php if (empty($lowStockItems)): ?>
      <div class="px-6 py-10 text-center text-slate-400 text-sm">All items are sufficiently stocked.</div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Product</th>
            <th class="px-6 py-3 text-left">SKU</th>
            <th class="px-6 py-3 text-right">Current Stock</th>
            <th class="px-6 py-3 text-right">Min. Required</th>
            <th class="px-6 py-3 text-left">Branch</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($lowStockItems as $item): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-800"><?= e($item['name'] ?? '') ?></td>
            <td class="px-6 py-3 text-slate-500"><?= e($item['sku'] ?? '—') ?></td>
            <td class="px-6 py-3 text-right font-semibold text-red-600"><?= e($item['stock'] ?? 0) ?></td>
            <td class="px-6 py-3 text-right text-slate-600"><?= e($item['min_stock'] ?? 0) ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($item['branch_name'] ?? '—') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-semibold text-slate-800">Stock by Branch</h3></div>
    <?php if (empty($stockByBranch)): ?>
      <div class="px-6 py-8 text-center text-slate-400 text-sm">No stock data.</div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Branch</th>
            <th class="px-6 py-3 text-right">Total Items</th>
            <th class="px-6 py-3 text-right">Total Value</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($stockByBranch as $br): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-800"><?= e($br['branch_name'] ?? '') ?></td>
            <td class="px-6 py-3 text-right text-slate-600"><?= number_format((int)($br['total_items'] ?? 0)) ?></td>
            <td class="px-6 py-3 text-right font-semibold text-sky-600"><?= number_format((float)($br['total_value'] ?? 0), 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>
