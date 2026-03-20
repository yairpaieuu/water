<?php $pageTitle = 'Attendance — ' . e($date); ob_start(); ?>
<?php include VIEW_PATH . '/partials/flash.php'; ?>
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h2 class="text-xl font-bold text-slate-800">Daily Attendance</h2>
    <form method="GET" action="/attendance" class="flex items-center gap-2">
      <label class="text-sm text-slate-600 font-medium">Date:</label>
      <input type="date" name="date" value="<?= e($date) ?>" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-500">
      <button type="submit" class="px-4 py-2 bg-sky-500 text-white text-sm font-semibold rounded-lg hover:bg-sky-600 transition-colors">Load</button>
    </form>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-800">Attendance for <?= e($date) ?></h3>
      <p class="text-sm text-slate-500 mt-0.5"><?= count($employees) ?> employees</p>
    </div>
    <?php if (empty($employees)): ?>
      <div class="px-6 py-10 text-center text-slate-400 text-sm">No employees found.</div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-4 py-3 text-left">Employee</th>
            <th class="px-4 py-3 text-left">Department</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Check In</th>
            <th class="px-4 py-3 text-left">Check Out</th>
            <th class="px-4 py-3 text-left">Save</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($employees as $emp):
            $existing = null;
            foreach ($attendance as $att) {
                if ($att['employee_id'] == $emp['id']) { $existing = $att; break; }
            }
            $currentStatus = $existing['status'] ?? '';
            $currentIn     = $existing['check_in'] ?? '';
            $currentOut    = $existing['check_out'] ?? '';
          ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-sky-500 flex items-center justify-center text-white text-xs font-bold">
                  <?= strtoupper(mb_substr($emp['first_name'] ?? 'E', 0, 1) . mb_substr($emp['last_name'] ?? '', 0, 1)) ?>
                </div>
                <div>
                  <p class="font-medium text-slate-800"><?= e($emp['first_name'] . ' ' . $emp['last_name']) ?></p>
                  <p class="text-xs text-slate-400"><?= e($emp['employee_code'] ?? '') ?></p>
                </div>
              </div>
            </td>
            <td class="px-4 py-3 text-slate-600"><?= ucfirst(e($emp['department'] ?? '—')) ?></td>
            <td colspan="4" class="px-4 py-3">
              <form method="POST" action="/attendance/store" class="flex flex-wrap items-center gap-2">
                <?= \App\Core\CSRF::field() ?>
                <input type="hidden" name="employee_id" value="<?= e($emp['id']) ?>">
                <input type="hidden" name="date" value="<?= e($date) ?>">
                <?php if (!empty($existing['id'])): ?>
                  <input type="hidden" name="attendance_id" value="<?= e($existing['id']) ?>">
                <?php endif; ?>
                <select name="status" class="border border-slate-300 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-sky-500">
                  <?php foreach (['present'=>'Present','absent'=>'Absent','late'=>'Late','half_day'=>'Half Day','leave'=>'Leave'] as $val => $label): ?>
                    <option value="<?= $val ?>" <?= ($currentStatus === $val) ? 'selected' : '' ?>><?= $label ?></option>
                  <?php endforeach; ?>
                </select>
                <input type="time" name="check_in" value="<?= e($currentIn) ?>" class="border border-slate-300 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-sky-500 w-28">
                <input type="time" name="check_out" value="<?= e($currentOut) ?>" class="border border-slate-300 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-sky-500 w-28">
                <button type="submit" class="px-3 py-1.5 bg-sky-500 text-white text-xs font-semibold rounded-lg hover:bg-sky-600 transition-colors">Save</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php $content = ob_get_clean(); include VIEW_PATH . '/layouts/app.php'; ?>
