<?php $pageTitle = 'Dashboard'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="space-y-6">
  <h2 class="text-xl font-bold text-slate-800">Dashboard</h2>

  <!-- KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      </div>
      <div>
        <p class="text-sm text-slate-500">Total Customers</p>
        <p class="text-2xl font-bold text-slate-800"><?= number_format((int)($totalCustomers ?? 0)) ?></p>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
      </div>
      <div>
        <p class="text-sm text-slate-500">Active Contracts</p>
        <p class="text-2xl font-bold text-slate-800"><?= number_format((int)($activeContracts ?? 0)) ?></p>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <p class="text-sm text-slate-500">Pending Jobs</p>
        <p class="text-2xl font-bold text-slate-800"><?= number_format((int)($pendingJobs ?? 0)) ?></p>
      </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div>
        <p class="text-sm text-slate-500">Monthly Revenue</p>
        <p class="text-2xl font-bold text-slate-800"><?= number_format((float)($monthlyRevenue ?? 0), 2) ?></p>
      </div>
    </div>
  </div>

  <!-- Charts + Low Stock -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-800">Revenue Sparkline</h3>
        <?php if (!empty($lowStockCount) && $lowStockCount > 0): ?>
          <a href="/reports/inventory" class="flex items-center gap-1 text-xs text-red-500 font-medium hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?= e($lowStockCount) ?> low stock items
          </a>
        <?php endif; ?>
      </div>
      <canvas id="revenueChart" height="80"></canvas>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col">
      <h3 class="font-semibold text-slate-800 mb-4">Services Due This Week</h3>
      <?php if (empty($servicesDue)): ?>
        <p class="text-sm text-slate-400 mt-2">No services due this week.</p>
      <?php else: ?>
      <ul class="space-y-3 flex-1 overflow-y-auto">
        <?php foreach ($servicesDue as $svc): ?>
        <li class="flex items-start gap-3 text-sm">
          <span class="w-2 h-2 rounded-full bg-sky-500 mt-1.5 flex-shrink-0"></span>
          <div>
            <p class="font-medium text-slate-800"><?= e($svc['customer_name'] ?? '—') ?></p>
            <p class="text-xs text-slate-500"><?= e($svc['job_type'] ?? '') ?> &middot; <?= e($svc['scheduled_date'] ?? '') ?></p>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <a href="/jobs?filter=upcoming" class="mt-4 text-xs text-sky-500 hover:underline self-end">View all &rarr;</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent Jobs & Sales -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-semibold text-slate-800">Recent Jobs</h3>
        <a href="/jobs" class="text-xs text-sky-500 hover:underline">View all</a>
      </div>
      <?php if (empty($recentJobs)): ?>
        <div class="px-6 py-8 text-center text-slate-400 text-sm">No recent jobs.</div>
      <?php else: ?>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Job</th>
            <th class="px-6 py-3 text-left">Customer</th>
            <th class="px-6 py-3 text-left">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($recentJobs as $job):
            $jc = ['completed'=>'bg-green-100 text-green-700','pending'=>'bg-amber-100 text-amber-700','in_progress'=>'bg-sky-100 text-sky-700','cancelled'=>'bg-red-100 text-red-700'];
            $jcol = $jc[$job['status'] ?? ''] ?? 'bg-slate-100 text-slate-600';
          ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3"><a href="/jobs/<?= e($job['id']) ?>" class="text-sky-500 hover:underline">#<?= e($job['id']) ?></a></td>
            <td class="px-6 py-3 text-slate-700"><?= e($job['customer_name'] ?? '—') ?></td>
            <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $jcol ?>"><?= ucfirst(str_replace('_',' ',e($job['status'] ?? ''))) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-semibold text-slate-800">Recent Sales</h3>
        <a href="/orders" class="text-xs text-sky-500 hover:underline">View all</a>
      </div>
      <?php if (empty($recentSales)): ?>
        <div class="px-6 py-8 text-center text-slate-400 text-sm">No recent sales.</div>
      <?php else: ?>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Customer</th>
            <th class="px-6 py-3 text-left">Date</th>
            <th class="px-6 py-3 text-right">Amount</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($recentSales as $sale): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-800"><?= e($sale['customer_name'] ?? '—') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($sale['date'] ?? '') ?></td>
            <td class="px-6 py-3 text-right font-semibold text-sky-600"><?= number_format((float)($sale['total'] ?? 0), 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
(function() {
  const revenueData = <?= json_encode(array_values(array_map(fn($m) => (float)($m['total'] ?? 0), $revenueChart ?? []))) ?>;
  const labels = <?= json_encode(array_values(array_map(fn($m) => $m['month'] ?? '', $revenueChart ?? []))) ?>;
  if (revenueData.length > 0) {
    new Chart(document.getElementById('revenueChart').getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Revenue',
          data: revenueData,
          borderColor: '#0ea5e9',
          backgroundColor: 'rgba(14,165,233,0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#0ea5e9',
          pointRadius: 4,
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
  }
})();
</script>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>
