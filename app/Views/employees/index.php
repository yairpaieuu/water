<?php $pageTitle = 'Employees'; ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h2 class="text-xl font-bold text-slate-800">Employees</h2>
    <div class="flex items-center gap-3">
      <form method="GET" action="/employees" class="flex gap-2">
        <select name="branch_id" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
          <option value="">All Branches</option>
          <?php foreach ($branches as $b): ?>
            <option value="<?= e($b['id']) ?>" <?= ($branchFilter == $b['id']) ? 'selected' : '' ?>><?= e($b['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
      <a href="/employees/create" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Employee
      </a>
    </div>
  </div>

  <?php if (empty($employees)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-16 text-center text-slate-400">
      <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <p class="font-medium">No employees found.</p>
    </div>
  <?php else: ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    <?php foreach ($employees as $emp):
      $initials = strtoupper(mb_substr($emp['first_name'] ?? 'E', 0, 1) . mb_substr($emp['last_name'] ?? '', 0, 1));
      $deptColors = ['management'=>'bg-purple-100 text-purple-700','sales'=>'bg-sky-100 text-sky-700','technical'=>'bg-amber-100 text-amber-700','admin'=>'bg-slate-100 text-slate-600'];
      $deptColor = $deptColors[$emp['department'] ?? ''] ?? 'bg-slate-100 text-slate-600';
      $isActive = ($emp['status'] ?? '') === 'active';
    ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col items-center text-center hover:shadow-md transition-shadow">
      <div class="w-16 h-16 rounded-full bg-sky-500 flex items-center justify-center text-white text-xl font-bold mb-3">
        <?= e($initials) ?>
      </div>
      <h3 class="font-semibold text-slate-800 text-sm"><?= e($emp['first_name'] . ' ' . $emp['last_name']) ?></h3>
      <p class="text-xs text-slate-500 mt-0.5"><?= e($emp['position'] ?? '—') ?></p>
      <p class="text-xs text-slate-400 mt-0.5"><?= e($emp['branch_name'] ?? '—') ?></p>
      <div class="mt-3 flex items-center gap-2 flex-wrap justify-center">
        <?php if (!empty($emp['department'])): ?>
          <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $deptColor ?>"><?= ucfirst(e($emp['department'])) ?></span>
        <?php endif; ?>
        <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span>
      </div>
      <a href="/employees/<?= e($emp['id']) ?>" class="mt-4 text-xs text-sky-500 hover:underline font-medium">View Profile &rarr;</a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>
