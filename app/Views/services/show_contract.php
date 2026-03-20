<?php $pageTitle = 'Contract #' . e($contract['id']); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="max-w-5xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-800">Contract #<?= e($contract['id']) ?></h2>
    <div class="flex gap-2">
      <a href="/jobs/create?contract_id=<?= e($contract['id']) ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Job
      </a>
      <a href="/contracts" class="px-4 py-2 border border-slate-300 text-sm text-slate-700 rounded-lg hover:bg-slate-50">&larr; Back</a>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
      <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-4">Contract Details</h3>
      <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
        <div><dt class="text-slate-500">Customer</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($customer['name'] ?? '') ?></dd></div>
        <div><dt class="text-slate-500">Phone</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($customer['phone'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Product</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($contract['product_name'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Serial Number</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($contract['serial_number'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Service Type</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($contract['service_type_name'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Branch</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($contract['branch_name'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Installation Date</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($contract['installation_date'] ?? '—') ?></dd></div>
        <div><dt class="text-slate-500">Assigned Technician</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($contract['technician_name'] ?? 'Unassigned') ?></dd></div>
        <div class="col-span-2"><dt class="text-slate-500">Location Notes</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($contract['location_notes'] ?? '—') ?></dd></div>
      </dl>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col items-center justify-center gap-3">
      <?php
        $status = $contract['status'] ?? 'active';
        $statusColors = ['active'=>'bg-green-100 text-green-700','inactive'=>'bg-slate-100 text-slate-600','expired'=>'bg-amber-100 text-amber-700','cancelled'=>'bg-red-100 text-red-700'];
        $color = $statusColors[$status] ?? 'bg-slate-100 text-slate-600';
      ?>
      <span class="px-4 py-2 rounded-full text-sm font-semibold <?= $color ?>"><?= ucfirst(e($status)) ?></span>
      <p class="text-xs text-slate-400">Contract Status</p>
      <div class="w-full mt-4 pt-4 border-t border-slate-100 text-center">
        <p class="text-2xl font-bold text-slate-800"><?= count($jobs) ?></p>
        <p class="text-sm text-slate-500">Total Jobs</p>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">Related Jobs</h3>
      <a href="/jobs/create?contract_id=<?= e($contract['id']) ?>" class="text-sm text-sky-500 hover:underline">+ New Job</a>
    </div>
    <?php if (empty($jobs)): ?>
      <div class="px-6 py-10 text-center text-slate-400 text-sm">No jobs found for this contract.</div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Job #</th>
            <th class="px-6 py-3 text-left">Type</th>
            <th class="px-6 py-3 text-left">Scheduled</th>
            <th class="px-6 py-3 text-left">Technician</th>
            <th class="px-6 py-3 text-left">Status</th>
            <th class="px-6 py-3 text-left">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($jobs as $job): ?>
          <?php
            $jStatus = $job['status'] ?? '';
            $jColors = ['completed'=>'bg-green-100 text-green-700','pending'=>'bg-amber-100 text-amber-700','in_progress'=>'bg-sky-100 text-sky-700','cancelled'=>'bg-red-100 text-red-700'];
            $jColor = $jColors[$jStatus] ?? 'bg-slate-100 text-slate-600';
          ?>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-3 font-medium text-slate-800">#<?= e($job['id']) ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($job['job_type'] ?? '—') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($job['scheduled_date'] ?? '—') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($job['technician_name'] ?? 'Unassigned') ?></td>
            <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $jColor ?>"><?= ucfirst(str_replace('_', ' ', e($jStatus))) ?></span></td>
            <td class="px-6 py-3"><a href="/jobs/<?= e($job['id']) ?>" class="text-sky-500 hover:underline text-xs">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
