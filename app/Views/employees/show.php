<?php $pageTitle = e(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '')); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<?php
  $initials = strtoupper(mb_substr($employee['first_name'] ?? 'E', 0, 1) . mb_substr($employee['last_name'] ?? '', 0, 1));
  $deptColors = ['management'=>'bg-purple-100 text-purple-700','sales'=>'bg-sky-100 text-sky-700','technical'=>'bg-amber-100 text-amber-700','admin'=>'bg-slate-100 text-slate-600'];
  $deptColor = $deptColors[$employee['department'] ?? ''] ?? 'bg-slate-100 text-slate-600';
  $isActive = ($employee['status'] ?? '') === 'active';
?>
<div class="max-w-4xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-800">Employee Profile</h2>
    <div class="flex gap-2">
      <a href="/employees/<?= e($employee['id']) ?>/edit" class="px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">Edit</a>
      <a href="/employees" class="px-4 py-2 border border-slate-300 text-sm text-slate-700 rounded-lg hover:bg-slate-50">&larr; Back</a>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
      <div class="w-24 h-24 rounded-full bg-sky-500 flex items-center justify-center text-white text-3xl font-bold flex-shrink-0">
        <?= e($initials) ?>
      </div>
      <div class="flex-1 text-center sm:text-left">
        <h3 class="text-2xl font-bold text-slate-800"><?= e($employee['first_name'] . ' ' . $employee['last_name']) ?></h3>
        <p class="text-slate-500 mt-1"><?= e($employee['position'] ?? '—') ?></p>
        <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-3">
          <?php if (!empty($employee['department'])): ?>
            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $deptColor ?>"><?= ucfirst(e($employee['department'])) ?></span>
          <?php endif; ?>
          <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span>
          <?php if (!empty($employee['employee_code'])): ?>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600"><?= e($employee['employee_code']) ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <hr class="my-6 border-slate-100">
    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-4 text-sm">
      <div><dt class="text-slate-500">Email</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($employee['email'] ?? '—') ?></dd></div>
      <div><dt class="text-slate-500">Phone</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($employee['phone'] ?? '—') ?></dd></div>
      <div><dt class="text-slate-500">Branch</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($branch['name'] ?? '—') ?></dd></div>
      <div><dt class="text-slate-500">Date Joined</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($employee['date_joined'] ?? '—') ?></dd></div>
      <div><dt class="text-slate-500">Salary</dt><dd class="font-medium text-slate-800 mt-0.5"><?= !empty($employee['salary']) ? number_format((float)$employee['salary'], 2) : '—' ?></dd></div>
      <div><dt class="text-slate-500">City</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($employee['city'] ?? '—') ?></dd></div>
      <div class="sm:col-span-2"><dt class="text-slate-500">Address</dt><dd class="font-medium text-slate-800 mt-0.5"><?= e($employee['address'] ?? '—') ?></dd></div>
    </dl>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">Recent Attendance</h3>
      <a href="/attendance?employee_id=<?= e($employee['id']) ?>" class="text-sm text-sky-500 hover:underline">View All</a>
    </div>
    <?php if (empty($recentAttendance)): ?>
      <div class="px-6 py-8 text-center text-slate-400 text-sm">No attendance records found.</div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-6 py-3 text-left">Date</th>
            <th class="px-6 py-3 text-left">Status</th>
            <th class="px-6 py-3 text-left">Check In</th>
            <th class="px-6 py-3 text-left">Check Out</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($recentAttendance as $att):
            $attColors = ['present'=>'bg-green-100 text-green-700','absent'=>'bg-red-100 text-red-700','late'=>'bg-amber-100 text-amber-700','half_day'=>'bg-orange-100 text-orange-700','leave'=>'bg-purple-100 text-purple-700'];
            $attColor = $attColors[$att['status'] ?? ''] ?? 'bg-slate-100 text-slate-600';
          ?>
          <tr class="hover:bg-slate-50">
            <td class="px-6 py-3 text-slate-800"><?= e($att['date'] ?? '') ?></td>
            <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $attColor ?>"><?= ucfirst(str_replace('_', ' ', e($att['status'] ?? ''))) ?></span></td>
            <td class="px-6 py-3 text-slate-600"><?= e($att['check_in'] ?? '—') ?></td>
            <td class="px-6 py-3 text-slate-600"><?= e($att['check_out'] ?? '—') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
