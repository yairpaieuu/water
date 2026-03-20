<?php $pageTitle = 'Sales Report'; ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h2 class="text-xl font-bold text-slate-800">Sales Report — <?= e($year) ?></h2>
    <form method="GET" action="/reports/sales" class="flex items-center gap-2">
      <select name="year" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
          <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>
      <button type="submit" class="px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">Go</button>
    </form>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Total Revenue</p>
      <p class="text-2xl font-bold text-slate-800 mt-1"><?= number_format((float)($totalRevenue ?? 0), 2) ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Total Orders</p>
      <p class="text-2xl font-bold text-slate-800 mt-1"><?= number_format((int)($totalOrders ?? 0)) ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Amount Paid</p>
      <p class="text-2xl font-bold text-green-600 mt-1"><?= number_format((float)($paidAmount ?? 0), 2) ?></p>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <h3 class="font-semibold text-slate-800 mb-4">Monthly Revenue</h3>
    <canvas id="salesChart" height="100"></canvas>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-semibold text-slate-800">Top Products</h3></div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Product</th>
            <th class="px-6 py-3 text-right">Units</th>
            <th class="px-6 py-3 text-right">Revenue</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (empty($topProducts)): ?>
            <tr><td colspan="3" class="px-6 py-6 text-center text-slate-400">No data.</td></tr>
          <?php else: ?>
          <?php foreach ($topProducts as $tp): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-800"><?= e($tp['name'] ?? '') ?></td>
            <td class="px-6 py-3 text-right text-slate-600"><?= e($tp['units'] ?? 0) ?></td>
            <td class="px-6 py-3 text-right text-slate-800 font-medium"><?= number_format((float)($tp['revenue'] ?? 0), 2) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-semibold text-slate-800">Recent Sales</h3></div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Customer</th>
            <th class="px-6 py-3 text-left">Date</th>
            <th class="px-6 py-3 text-right">Amount</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (empty($recentSales)): ?>
            <tr><td colspan="3" class="px-6 py-6 text-center text-slate-400">No data.</td></tr>
          <?php else: ?>
          <?php foreach ($recentSales as $rs): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-800"><?= e($rs['customer_name'] ?? '') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($rs['date'] ?? '') ?></td>
            <td class="px-6 py-3 text-right text-slate-800 font-medium"><?= number_format((float)($rs['total'] ?? 0), 2) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
const monthlyData = <?= json_encode(array_values(array_map(fn($m) => (float)($m['total'] ?? 0), $monthlyRevenue))) ?>;
const labels = <?= json_encode(array_values(array_map(fn($m) => $m['month'] ?? '', $monthlyRevenue))) ?>;
new Chart(ctx, {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: 'Revenue',
      data: monthlyData,
      borderColor: '#0ea5e9',
      backgroundColor: 'rgba(14,165,233,0.1)',
      borderWidth: 2,
      fill: true,
      tension: 0.4,
      pointBackgroundColor: '#0ea5e9',
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } }
    }
  }
});
</script>
