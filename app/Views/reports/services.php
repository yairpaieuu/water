<?php $pageTitle = 'Services Report'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="space-y-6">
  <h2 class="text-xl font-bold text-slate-800">Services Report</h2>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Total Jobs</p>
      <p class="text-2xl font-bold text-slate-800 mt-1"><?= number_format((int)($totalJobs ?? 0)) ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Completed</p>
      <p class="text-2xl font-bold text-green-600 mt-1"><?= number_format((int)($completedJobs ?? 0)) ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <p class="text-sm text-slate-500">Pending</p>
      <p class="text-2xl font-bold text-amber-500 mt-1"><?= number_format((int)($pendingJobs ?? 0)) ?></p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
      <h3 class="font-semibold text-slate-800 mb-4">Jobs by Status</h3>
      <canvas id="statusChart" height="220"></canvas>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
      <h3 class="font-semibold text-slate-800 mb-4">Jobs by Type</h3>
      <canvas id="typeChart" height="220"></canvas>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-semibold text-slate-800">Upcoming Services</h3></div>
    <?php if (empty($upcomingServices)): ?>
      <div class="px-6 py-10 text-center text-slate-400 text-sm">No upcoming services.</div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Job #</th>
            <th class="px-6 py-3 text-left">Customer</th>
            <th class="px-6 py-3 text-left">Type</th>
            <th class="px-6 py-3 text-left">Scheduled</th>
            <th class="px-6 py-3 text-left">Technician</th>
            <th class="px-6 py-3 text-left">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($upcomingServices as $svc):
            $sc = ['pending'=>'bg-amber-100 text-amber-700','in_progress'=>'bg-sky-100 text-sky-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700'];
            $sc2 = $sc[$svc['status'] ?? ''] ?? 'bg-slate-100 text-slate-600';
          ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 font-medium text-slate-800"><a href="/jobs/<?= e($svc['id']) ?>" class="text-sky-500 hover:underline">#<?= e($svc['id']) ?></a></td>
            <td class="px-6 py-3 text-slate-700"><?= e($svc['customer_name'] ?? '—') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($svc['job_type'] ?? '—') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($svc['scheduled_date'] ?? '—') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($svc['technician_name'] ?? 'Unassigned') ?></td>
            <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $sc2 ?>"><?= ucfirst(str_replace('_', ' ', e($svc['status'] ?? ''))) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
<script>
(function() {
  const statusData = <?= json_encode(array_values(array_map(fn($s) => (int)($s['count'] ?? 0), $jobsByStatus ?? []))) ?>;
  const statusLabels = <?= json_encode(array_values(array_map(fn($s) => ucfirst(str_replace('_', ' ', $s['status'] ?? '')), $jobsByStatus ?? []))) ?>;
  new Chart(document.getElementById('statusChart').getContext('2d'), {
    type: 'doughnut',
    data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: ['#0ea5e9','#f59e0b','#22c55e','#ef4444','#8b5cf6'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });

  const typeData = <?= json_encode(array_values(array_map(fn($t) => (int)($t['count'] ?? 0), $jobsByType ?? []))) ?>;
  const typeLabels = <?= json_encode(array_values(array_map(fn($t) => $t['job_type'] ?? '', $jobsByType ?? []))) ?>;
  new Chart(document.getElementById('typeChart').getContext('2d'), {
    type: 'doughnut',
    data: { labels: typeLabels, datasets: [{ data: typeData, backgroundColor: ['#6366f1','#ec4899','#14b8a6','#f97316','#84cc16'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });
})();
</script>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>
